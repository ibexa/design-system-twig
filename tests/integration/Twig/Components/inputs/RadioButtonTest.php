<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\inputs;

use Ibexa\DesignSystemTwig\Twig\Components\inputs\RadioButton;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class RadioButtonTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            RadioButton::class,
            [
                'disabled' => true,
                'required' => true,
                'error' => true,
                'attributes' => ['class' => 'extra-class', 'data-test' => 'custom-data'],
            ]
        );

        self::assertInstanceOf(RadioButton::class, $component, 'Component should mount as inputs\\RadioButton.');
        self::assertTrue($component->disabled, 'Prop "disabled" should be true.');
        self::assertTrue($component->required, 'Prop "required" should be true.');
        self::assertTrue($component->error, 'Prop "error" should be true.');
    }

    public function testDefaultRenderProducesRadioInputWithBaseClasses(): void
    {
        $crawler = $this->renderTwigComponent(RadioButton::class, [])->crawler();

        $input = $this->getInput($crawler);
        self::assertSame('radio', $input->attr('type'), 'Rendered input should be type="radio".');

        $class = $this->getInputClass($input);
        self::assertStringContainsString('ids-input', $class, 'Base class "ids-input" should be present.');
        self::assertStringContainsString('ids-input--radio', $class, 'Modifier class "ids-input--radio" should be present.');
        self::assertStringContainsString('ids-input--medium', $class, 'Size class "ids-input--medium" should be present.');
        self::assertStringNotContainsString('ids-input--disabled', $class, 'Disabled modifier should not be present by default.');
        self::assertStringNotContainsString('ids-input--error', $class, 'Error modifier should not be present by default.');
        self::assertStringNotContainsString('ids-input--required', $class, 'Required modifier should not be present by default.');
    }

    public function testBooleanPropsAddExpectedClassesAndAttributes(): void
    {
        $crawler = $this->renderTwigComponent(
            RadioButton::class,
            [
                'disabled' => true,
                'required' => true,
                'error' => true,
            ]
        )->crawler();

        $input = $this->getInput($crawler);
        $class = $this->getInputClass($input);

        self::assertStringContainsString('ids-input--disabled', $class, 'Disabled should add "ids-input--disabled" class.');
        self::assertStringContainsString('ids-input--required', $class, 'Required should add "ids-input--required" class.');
        self::assertStringContainsString('ids-input--error', $class, 'Error should add "ids-input--error" class.');
        self::assertNotNull($input->attr('disabled'), 'Disabled should add the "disabled" attribute.');
        self::assertNotNull($input->attr('required'), 'Required should add the "required" attribute.');
    }

    public function testAttributesBagMergesClassesAndArbitraryAttributes(): void
    {
        $crawler = $this->renderTwigComponent(
            RadioButton::class,
            [
                'attributes' => [
                    'class' => 'extra-class',
                    'data-custom' => 'custom',
                    'checked' => true,
                    'name' => 'group',
                    'value' => 'foo',
                ],
            ]
        )->crawler();

        $input = $this->getInput($crawler);
        $class = $this->getInputClass($input);

        self::assertStringContainsString('extra-class', $class, 'Custom class from attributes should be merged into class attribute.');
        self::assertSame('custom', $input->attr('data-custom'), 'Custom data attribute should be rendered.');
        self::assertNotNull($input->attr('checked'), 'Boolean "checked" from attributes should be rendered.');
        self::assertSame('group', $input->attr('name'), 'Input "name" should pass through attributes.');
        self::assertSame('foo', $input->attr('value'), 'Input "value" should pass through attributes.');
    }

    public function testInvalidTypesCauseResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(RadioButton::class, [
            'required' => 'yes',
        ]);
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function getInput(Crawler $crawler): Crawler
    {
        $input = $crawler->filter('.ids-radio-button > input')->first();
        self::assertGreaterThan(0, $input->count(), 'Input element not found under .ids-radio-button.');

        return $input;
    }

    private function getInputClass(Crawler $input): string
    {
        return (string)$input->attr('class');
    }
}
