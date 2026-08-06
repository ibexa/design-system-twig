<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\RadioButton;

use Ibexa\DesignSystemTwig\Twig\Components\RadioButton\Input;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class InputTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Input::class, [
            'name' => 'group',
            'value' => 'A',
            'required' => true,
            'disabled' => true,
            'error' => true,
            'size' => 'small',
        ]);

        self::assertInstanceOf(Input::class, $component, 'Component should mount as RadioButton\\Input.');
        self::assertSame('group', $component->name, 'Prop "name" should be set.');
        self::assertTrue($component->required, 'Prop "required" should be true.');
        self::assertTrue($component->disabled, 'Prop "disabled" should be true.');
        self::assertSame('radio', $component->getType(), 'getType() should return "radio".');
    }

    public function testDefaultRenderProducesInputOnly(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'name' => 'group',
            'value' => 'A',
        ])->crawler();

        $input = $this->getInput($crawler);
        self::assertSame('radio', $input->attr('type'), 'Input "type" should be "radio".');
        self::assertSame('group', $input->attr('name'), 'Input "name" should equal provided name.');
        self::assertSame('A', $input->attr('value'), 'Input "value" should equal provided value.');

        $inputClass = $this->getClassAttr($input);
        self::assertStringContainsString('ids-input', $inputClass, 'Input should have base class "ids-input".');
        self::assertStringContainsString('ids-input--radio', $inputClass, 'Input should have "ids-input--radio" modifier.');
        self::assertStringContainsString('ids-input--medium', $inputClass, 'Input should have default size "ids-input--medium".');
    }

    public function testAttributesMergeClassAndInputGetsDataFromAttributes(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'name' => 'group',
            'value' => 'A',
            'attributes' => [
                'class' => 'extra-class',
                'data-custom' => 'custom',
            ],
        ])->crawler();

        $input = $this->getInput($crawler);
        self::assertStringContainsString('extra-class', $this->getClassAttr($input), 'Custom class should merge into input class attribute.');
        self::assertSame('custom', $input->attr('data-custom'), 'Custom data attribute should be rendered on the input element.');
    }

    public function testBooleanPropsAddClassesAndNativeAttributes(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'name' => 'group',
            'value' => 'A',
            'required' => true,
            'disabled' => true,
            'error' => true,
        ])->crawler();

        $input = $this->getInput($crawler);

        self::assertNotNull($input->attr('required'), '"required" should render native "required" attribute.');
        self::assertNotNull($input->attr('disabled'), '"disabled" should render native "disabled" attribute.');

        $class = $this->getClassAttr($input);
        self::assertStringContainsString('ids-input--required', $class, '"required" should add "ids-input--required" class.');
        self::assertStringContainsString('ids-input--disabled', $class, '"disabled" should add "ids-input--disabled" class.');
        self::assertStringContainsString('ids-input--error', $class, '"error" should add "ids-input--error" class.');
    }

    public function testSizeVariantSmallAddsClass(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'name' => 'group',
            'value' => 'A',
            'size' => 'small',
        ])->crawler();

        $input = $this->getInput($crawler);
        self::assertStringContainsString('ids-input--small', $this->getClassAttr($input), 'Size "small" should add "ids-input--small" class.');
    }

    public function testEmptyNameCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Input::class, [
            'name' => '',
            'value' => 'A',
        ]);
    }

    public function testInvalidDisabledTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Input::class, [
            'name' => 'group',
            'value' => 'A',
            'disabled' => 'yes',
        ]);
    }

    public function testInvalidSizeValueCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Input::class, [
            'name' => 'group',
            'value' => 'A',
            'size' => 'giant',
        ]);
    }

    public function testNameIsOptional(): void
    {
        $component = $this->mountTwigComponent(Input::class, [
            'value' => 'A',
        ]);

        self::assertInstanceOf(Input::class, $component, 'Component should mount without a "name" prop.');
        self::assertNull($component->name, 'Prop "name" should default to null.');

        $crawler = $this->renderTwigComponent(Input::class, [
            'value' => 'A',
        ])->crawler();

        self::assertNull($this->getInput($crawler)->attr('name'), 'Input should not render a "name" attribute.');
    }

    private function getInput(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('input.ids-input--radio')->first();
        self::assertGreaterThan(0, $node->count(), 'Input should be present.');

        return $node;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }
}
