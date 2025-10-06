<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\inputs;

use Ibexa\DesignSystemTwig\Twig\Components\inputs\Checkbox;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class CheckboxTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            Checkbox::class,
            [
                'disabled' => true,
                'required' => true,
                'error' => true,
                'attributes' => ['class' => 'extra-class', 'data-test' => 'custom-data'],
            ]
        );

        self::assertInstanceOf(Checkbox::class, $component, 'Component should mount as Checkbox.');
        self::assertTrue($component->disabled, 'Property "disabled" should be true.');
        self::assertTrue($component->required, 'Property "required" should be true.');
        self::assertTrue($component->error, 'Property "error" should be true.');
    }

    public function testDefaultRenderProducesCheckboxInputWithBaseClasses(): void
    {
        $rendered = $this->renderTwigComponent(Checkbox::class);

        $input = $this->getInput($rendered->crawler());

        self::assertSame('checkbox', $input->attr('type'), 'Rendered input should be type="checkbox".');

        $class = $this->getInputClass($input);
        self::assertStringContainsString('ids-input', $class, 'Base class "ids-input" should be present.');
        self::assertStringContainsString('ids-input--checkbox', $class, 'Modifier class "ids-input--checkbox" should be present.');
        self::assertStringContainsString('ids-input--medium', $class, 'Size class "ids-input--medium" should be present.');
    }

    public function testBooleanPropsAddExpectedClassesAndAttributes(): void
    {
        $rendered = $this->renderTwigComponent(
            Checkbox::class,
            [
                'disabled' => true,
                'required' => true,
                'error' => true,
            ]
        );

        $input = $this->getInput($rendered->crawler());
        $class = $this->getInputClass($input);

        self::assertStringContainsString('ids-input--disabled', $class, 'Disabled should add "ids-input--disabled" class.');
        self::assertStringContainsString('ids-input--required', $class, 'Required should add "ids-input--required" class.');
        self::assertStringContainsString('ids-input--error', $class, 'Error should add "ids-input--error" class.');

        self::assertNotNull($input->attr('disabled'), 'Disabled should add the "disabled" attribute.');
        self::assertNotNull($input->attr('required'), 'Required should add the "required" attribute.');
    }

    public function testAttributesBagMergesClassesAndArbitraryAttributes(): void
    {
        $rendered = $this->renderTwigComponent(
            Checkbox::class,
            [
                'attributes' => [
                    'class' => 'extra-class',
                    'custom-data' => 'extra-data',
                    'checked' => true,
                ],
            ]
        );

        $input = $this->getInput($rendered->crawler());
        $class = $this->getInputClass($input);

        self::assertStringContainsString('extra-class', $class, 'Custom class from attributes should be merged into class attribute.');
        self::assertSame('extra-data', $input->attr('custom-data'), 'Custom data attribute should be rendered.');
        self::assertNotNull($input->attr('checked'), 'Boolean "checked" from attributes should be rendered.');
    }

    public function testInvalidTypesCauseResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Checkbox::class, [
            'required' => 'yes',
        ]);
    }

    private function getInput(Crawler $crawler): Crawler
    {
        $input = $crawler->filter('.ids-checkbox > input')->first();
        self::assertGreaterThan(0, $input->count(), 'Input element not found under .ids-checkbox.');

        return $input;
    }

    private function getInputClass(Crawler $input): string
    {
        return (string)$input->attr('class');
    }
}
