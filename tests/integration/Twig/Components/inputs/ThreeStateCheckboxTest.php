<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\inputs;

use Ibexa\DesignSystemTwig\Twig\Components\inputs\ThreeStateCheckbox;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ThreeStateCheckboxTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            ThreeStateCheckbox::class,
            [
                'disabled' => true,
                'required' => true,
                'error' => true,
                'indeterminate' => true,
                'attributes' => ['class' => 'extra-class', 'data-custom' => 'custom', 'value' => 'foo'],
            ]
        );

        self::assertInstanceOf(ThreeStateCheckbox::class, $component, 'Component should mount as inputs\\ThreeStateCheckbox.');
        self::assertTrue($component->disabled, 'Prop "disabled" should be true.');
        self::assertTrue($component->required, 'Prop "required" should be true.');
        self::assertTrue($component->error, 'Prop "error" should be true.');
        self::assertTrue($component->indeterminate, 'Prop "indeterminate" should be true.');
    }

    public function testDefaultRenderProducesCheckboxInputWithBaseClasses(): void
    {
        $crawler = $this->renderTwigComponent(ThreeStateCheckbox::class, [])->crawler();

        $input = $this->getInput($crawler);
        self::assertSame('checkbox', $input->attr('type'), 'Rendered input should be type="checkbox".');

        $class = $this->getClassAttr($input);
        self::assertStringContainsString('ids-input', $class, 'Base class "ids-input" should be present.');
        self::assertStringContainsString('ids-input--checkbox', $class, 'Modifier class "ids-input--checkbox" should be present.');
        self::assertStringContainsString('ids-input--medium', $class, 'Size class "ids-input--medium" should be present.');
        self::assertStringNotContainsString('ids-input--disabled', $class, 'Disabled modifier should not be present by default.');
        self::assertStringNotContainsString('ids-input--error', $class, 'Error modifier should not be present by default.');
        self::assertStringNotContainsString('ids-input--required', $class, 'Required modifier should not be present by default.');
        self::assertStringNotContainsString('ids-input--indeterminate', $class, 'Indeterminate modifier should not be present by default.');
    }

    public function testBooleanPropsAddExpectedClassesAndAttributes(): void
    {
        $crawler = $this->renderTwigComponent(
            ThreeStateCheckbox::class,
            [
                'disabled' => true,
                'required' => true,
                'error' => true,
                'indeterminate' => true,
            ]
        )->crawler();

        $input = $this->getInput($crawler);
        $class = $this->getClassAttr($input);

        self::assertStringContainsString('ids-input--disabled', $class, 'Disabled should add "ids-input--disabled" class.');
        self::assertStringContainsString('ids-input--required', $class, 'Required should add "ids-input--required" class.');
        self::assertStringContainsString('ids-input--error', $class, 'Error should add "ids-input--error" class.');
        self::assertStringContainsString('ids-input--indeterminate', $class, 'Indeterminate should add "ids-input--indeterminate" class.');

        self::assertNotNull($input->attr('disabled'), 'Disabled should add the "disabled" attribute.');
        self::assertNotNull($input->attr('required'), 'Required should add the "required" attribute.');
    }

    public function testAttributesBagMergesClassesAndArbitraryAttributes(): void
    {
        $crawler = $this->renderTwigComponent(
            ThreeStateCheckbox::class,
            [
                'attributes' => [
                    'class' => 'extra-class',
                    'data-custom' => 'custom',
                    'value' => 'foo',
                    'checked' => true,
                ],
            ]
        )->crawler();

        $input = $this->getInput($crawler);
        $class = $this->getClassAttr($input);

        self::assertStringContainsString('extra-class', $class, 'Custom class from attributes should be merged into class attribute.');
        self::assertSame('custom', $input->attr('data-custom'), 'Custom data attribute should be rendered.');
        self::assertSame('foo', $input->attr('value'), 'Input "value" should pass through attributes.');
        self::assertNotNull($input->attr('checked'), 'Boolean "checked" from attributes should be rendered.');
    }

    public function testInvalidTypesCauseResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(ThreeStateCheckbox::class, [
            'indeterminate' => 'yes',
        ]);
    }

    private function getInput(Crawler $crawler): Crawler
    {
        $input = $crawler->filter('.ids-three-state-checkbox > input')->first();
        self::assertGreaterThan(0, $input->count(), 'Input element not found under .ids-three-state-checkbox.');

        return $input;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }
}
