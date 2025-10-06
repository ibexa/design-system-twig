<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\Checkbox;

use Ibexa\DesignSystemTwig\Twig\Components\Checkbox\Input;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class InputTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Input::class, [
            'id' => 'agree',
            'name' => 'terms',
            'value' => 'yes',
            'required' => true,
            'disabled' => true,
            'indeterminate' => true,
        ]);

        self::assertInstanceOf(Input::class, $component, 'Component should mount as Checkbox\\Input.');
        self::assertSame('terms', $component->name, 'Prop "name" should be set on the component.');
        self::assertTrue($component->required, 'Prop "required" should be true.');
        self::assertTrue($component->disabled, 'Prop "disabled" should be true.');
        self::assertSame('checkbox', $component->getType(), 'getType() should return "checkbox".');
    }

    public function testDefaultRenderProducesWrapperAndInput(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'id' => 'agree',
            'name' => 'terms',
            'value' => 'yes',
        ])->crawler();

        $wrapper = $this->getWrapper($crawler);
        $wrapperClass = $this->getClassAttr($wrapper);

        self::assertStringContainsString('ids-choice-input', $wrapperClass, 'Wrapper should contain base class "ids-choice-input".');
        self::assertStringContainsString('ids-checkbox', $wrapperClass, 'Wrapper should contain variant class "ids-checkbox".');

        $input = $this->getInput($crawler);
        self::assertSame('checkbox', $input->attr('type'), 'Input "type" should be "checkbox".');
        self::assertSame('agree', $input->attr('id'), 'Input "id" should equal provided id.');
        self::assertSame('terms', $input->attr('name'), 'Input "name" should equal provided name.');
        self::assertSame('yes', $input->attr('value'), 'Input "value" should equal provided value.');
    }

    public function testWrapperAttributesMergeClass(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'id' => 'agree',
            'name' => 'terms',
            'value' => 'yes',
            'attributes' => ['class' => 'extra-class'],
        ])->crawler();

        $wrapper = $this->getWrapper($crawler);
        $wrapperClass = $this->getClassAttr($wrapper);

        self::assertStringContainsString('extra-class', $wrapperClass, 'Custom class should merge into wrapper class attribute.');
    }

    public function testBooleanPropsRenderNativeAttributesAndIndeterminateClass(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'id' => 'agree',
            'name' => 'terms',
            'value' => 'yes',
            'required' => true,
            'disabled' => true,
            'indeterminate' => true,
        ])->crawler();

        $input = $this->getInput($crawler);
        self::assertNotNull($input->attr('required'), '"required" prop should render native "required" attribute.');
        self::assertNotNull($input->attr('disabled'), '"disabled" prop should render native "disabled" attribute.');

        $inputClass = $this->getClassAttr($input);
        self::assertStringContainsString('ids-input--indeterminate', $inputClass, '"indeterminate" should add "ids-input--indeterminate" class.');
    }

    public function testInvalidIndeterminateTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Input::class, [
            'id' => 'agree',
            'name' => 'terms',
            'value' => 'yes',
            'indeterminate' => 'not-bool',
        ]);
    }

    public function testMissingRequiredOptionsCauseResolverErrorOnMount(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->mountTwigComponent(Input::class, [
            'value' => 'yes',
        ]);
    }

    private function getWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-checkbox')->first();
        self::assertGreaterThan(0, $node->count(), 'Wrapper ".ids-checkbox" should be present.');

        return $node;
    }

    private function getInput(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-checkbox input')->first();
        if ($node->count() === 0) {
            $node = $crawler->filter('input')->first();
        }
        self::assertGreaterThan(0, $node->count(), 'Input element should be present.');

        return $node;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }
}
