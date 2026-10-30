<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\DropdownSingle;

use Ibexa\DesignSystemTwig\Twig\Components\DropdownSingle\Input;
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
        $component = $this->mountTwigComponent(Input::class, $this->baseProps(['value' => 'foo']));

        self::assertInstanceOf(
            Input::class,
            $component,
            'Component should mount as DropdownSingle\\Input.'
        );
    }

    public function testEmptyStateShowsPlaceholderAndHidesSelectedItems(): void
    {
        $crawler = $this->renderTwigComponent(
            Input::class,
            $this->baseProps()
        )->crawler();

        $select = $this->getSelect($crawler);
        $selectedOption = $select->filter('option[selected]')->first();
        self::assertSame(
            'a',
            $selectedOption->attr('value'),
            'Without a value, the first option should be selected by default.'
        );
        self::assertSame(
            'Alpha',
            trim($selectedOption->text('')),
            'The first option label should be shown as selected by default.'
        );

        $placeholder = $crawler->filter('.ids-dropdown__placeholder')->first();
        $selectedBox = $crawler->filter('.ids-dropdown__selection-info-items')->first();

        self::assertNotNull(
            $placeholder->attr('hidden'),
            'Placeholder should be hidden when no explicit value is selected.'
        );
        self::assertNull(
            $selectedBox->attr('hidden'),
            'Selection box should be visible when no explicit value is selected.'
        );
        self::assertStringContainsString(
            'Alpha',
            trim($selectedBox->text('')),
            'Selection box should show the first item label by default.'
        );
    }

    public function testSelectedValueHidesPlaceholderAndShowsSelectedLabel(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, $this->baseProps(['value' => 'b']))->crawler();

        $select = $this->getSelect($crawler);
        $selectedOption = $select->filter('option[selected]')->first();

        self::assertSame(
            'b',
            $selectedOption->attr('value'),
            'Selected <option> should match provided value.'
        );
        self::assertSame(
            'Beta',
            trim($selectedOption->text('')),
            'Selected <option> text should be the item label.'
        );

        $placeholder = $crawler->filter('.ids-dropdown__placeholder')->first();
        $selectedBox = $crawler->filter('.ids-dropdown__selection-info-items')->first();

        self::assertNotNull(
            $placeholder->attr('hidden'),
            'Placeholder should be hidden when a value is selected.'
        );
        self::assertNull(
            $selectedBox->attr('hidden'),
            'Selection box should be visible when a value is selected.'
        );
        self::assertStringContainsString(
            'Beta',
            trim($selectedBox->text('')),
            'Selection box should render selected label.'
        );
    }

    public function testEmptyPlaceholderFallsBackToAll(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, $this->baseProps([
            'placeholder' => '   ',
            'value' => '',
            'items' => [
                ['id' => '', 'label' => 'All'],
                ['id' => 'a', 'label' => 'Alpha'],
                ['id' => 'b', 'label' => 'Beta'],
            ],
        ]))->crawler();

        self::assertSame(
            'All',
            trim($crawler->filter('.ids-dropdown__selection-info-items')->first()->text('')),
            'Selected empty option should display the DS fallback label.'
        );
    }

    public function testOptionsAreRenderedAndOneIsMarkedSelected(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, $this->baseProps(['value' => 'a']))->crawler();

        $select = $this->getSelect($crawler);
        $options = $select->filter('option');

        self::assertSame(
            3,
            $options->count(),
            'Should render three <option> elements.'
        );
        self::assertSame(
            'a',
            $select->filter('option[selected]')->attr('value'),
            'The <option> with selected attribute should match provided value.'
        );

        $labels = $options->each(static fn (Crawler $o): string => trim($o->text('')));

        self::assertSame(
            ['Alpha', 'Beta', 'Gamma'],
            $labels,
            'Options should render provided labels in order.'
        );
    }

    public function testDisabledAndErrorAddClassesAndSelectDisabled(): void
    {
        $crawler = $this->renderTwigComponent(
            Input::class,
            $this->baseProps(['disabled' => true, 'error' => true])
        )->crawler();

        $wrapper = $this->getWrapper($crawler);
        $widget = $this->getWidget($crawler);
        $select = $this->getSelect($crawler);

        self::assertStringContainsString(
            'ids-dropdown--disabled',
            $this->getClassAttr($wrapper),
            'Wrapper should include disabled modifier.'
        );
        self::assertStringContainsString(
            'ids-dropdown--error',
            $this->getClassAttr($wrapper),
            'Wrapper should include error modifier.'
        );
        self::assertStringContainsString(
            'ids-input--disabled',
            $this->getClassAttr($widget),
            'Widget should include disabled modifier.'
        );
        self::assertStringContainsString(
            'ids-input--error',
            $this->getClassAttr($widget),
            'Widget should include error modifier.'
        );
        self::assertNotNull(
            $select->attr('disabled'),
            'Native "disabled" attribute should be present on <select> when disabled=true.'
        );
    }

    public function testChipTypeRendersChipWidgetWithoutInputChrome(): void
    {
        $crawler = $this->renderTwigComponent(
            Input::class,
            $this->baseProps([
                'type' => 'chip',
                'size' => 'small',
                'icon' => 'translation-language',
                'label' => 'Language:',
                'value' => 'b',
            ])
        )->crawler();

        $wrapper = $this->getWrapper($crawler);
        $widget = $this->getWidget($crawler);
        $icon = $crawler->filter('.ids-dropdown__widget > .ids-dropdown__icon');
        $label = $crawler->filter('.ids-dropdown__widget > .ids-dropdown__label')->first();

        self::assertStringContainsString(
            'ids-dropdown--chip',
            $this->getClassAttr($wrapper),
            'Wrapper should include the chip modifier.'
        );
        self::assertStringContainsString(
            'ids-dropdown--small',
            $this->getClassAttr($wrapper),
            'Wrapper should include the size modifier for the chip.'
        );
        self::assertStringNotContainsString(
            'ids-input',
            $this->getClassAttr($widget),
            'Chip widget should not carry the input classes.'
        );
        self::assertSame(1, $icon->count(), 'Chip should render the leading icon.');
        self::assertSame('true', $icon->attr('aria-hidden'), 'Chip icon should be decorative.');
        self::assertStringContainsString(
            'ids-icon--tiny',
            $icon->html(),
            'Small chip should render the tiny icon size.'
        );
        self::assertSame('Language:', trim($label->text('')), 'Chip should render the static label.');
        self::assertStringContainsString(
            'Beta',
            trim($crawler->filter('.ids-dropdown__selection-info-items')->first()->text('')),
            'Chip should render the selected label.'
        );
    }

    public function testInputTypeRendersNoChipParts(): void
    {
        $crawler = $this->renderTwigComponent(
            Input::class,
            $this->baseProps(['icon' => 'translation-language', 'label' => 'Language:'])
        )->crawler();

        $wrapper = $this->getWrapper($crawler);

        self::assertStringNotContainsString(
            'ids-dropdown--chip',
            $this->getClassAttr($wrapper),
            'Input type should not include the chip modifier.'
        );
        self::assertStringContainsString(
            'ids-input',
            $this->getClassAttr($this->getWidget($crawler)),
            'Input type widget should keep the input classes.'
        );
        self::assertCount(
            0,
            $crawler->filterXPath('//*[contains(concat(" ", normalize-space(@class), " "), " ids-dropdown__icon ")]'),
            'Input type should ignore the chip icon.'
        );
        self::assertCount(0, $crawler->filter('.ids-dropdown__label'), 'Input type should ignore the chip label.');
        self::assertNull($wrapper->attr('icon'), 'The icon prop should be consumed, not rendered as an attribute.');
        self::assertNull($wrapper->attr('label'), 'The label prop should be consumed, not rendered as an attribute.');
    }

    public function testInvalidTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Input::class, $this->baseProps(['type' => 'pill']));
    }

    public function testWrapperClassMergesFromAttributes(): void
    {
        $crawler = $this->renderTwigComponent(
            Input::class,
            $this->baseProps([
                'attributes' => ['class' => 'extra-class'],
            ])
        )->crawler();

        $wrapper = $this->getWrapper($crawler);

        self::assertStringContainsString(
            'extra-class',
            $this->getClassAttr($wrapper),
            'Custom class should be merged into wrapper classes.'
        );
    }

    public function testInvalidItemsTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Input::class, [
            'name' => 'group',
            'items' => 'not-an-array',
        ]);
    }

    public function testMissingRequiredOptionsCauseResolverErrorOnMount(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->mountTwigComponent(Input::class);
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function baseProps(array $overrides = []): array
    {
        return array_replace([
            'name' => 'group',
            'items' => [
                ['id' => 'a', 'label' => 'Alpha'],
                ['id' => 'b', 'label' => 'Beta'],
                ['id' => 'c', 'label' => 'Gamma'],
            ],
        ], $overrides);
    }

    private function getWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-dropdown')->first();
        self::assertGreaterThan(0, $node->count(), 'Wrapper ".ids-dropdown" should be present.');

        return $node;
    }

    private function getWidget(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-dropdown__widget')->first();
        self::assertGreaterThan(0, $node->count(), 'Widget ".ids-dropdown__widget" should be present.');

        return $node;
    }

    private function getSelect(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-dropdown__source > select')->first();
        self::assertGreaterThan(0, $node->count(), 'Source <select> should be present.');

        return $node;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }
}
