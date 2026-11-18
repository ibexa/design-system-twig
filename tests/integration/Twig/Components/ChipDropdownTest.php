<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\ChipDropdown;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

#[CoversClass(ChipDropdown::class)]
final class ChipDropdownTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(ChipDropdown::class, $this->baseProps(['value' => 'b']));

        self::assertInstanceOf(ChipDropdown::class, $component, 'Component should mount as ChipDropdown.');
        self::assertSame('b', $component->value, '"value" prop should be set on the component.');
        self::assertSame('Beta', $component->getSelectedLabel(), 'The selected label should follow the value.');
    }

    public function testZeroValueIsKept(): void
    {
        $component = $this->mountTwigComponent(ChipDropdown::class, [
            'name' => 'language',
            'items' => [['id' => 'x', 'label' => 'Xray'], ['id' => '0', 'label' => 'Zero']],
            'value' => '0',
        ]);

        self::assertInstanceOf(ChipDropdown::class, $component, 'Component should mount as ChipDropdown.');
        self::assertSame('0', $component->value, 'A "0" value is a value, not an empty one.');
        self::assertSame('Zero', $component->getSelectedLabel(), 'The "0" item should be the selected one.');
    }

    public function testFirstItemIsSelectedWhenValueIsEmpty(): void
    {
        $component = $this->mountTwigComponent(ChipDropdown::class, $this->baseProps());

        self::assertInstanceOf(ChipDropdown::class, $component, 'Component should mount as ChipDropdown.');
        self::assertSame('a', $component->value, 'An empty value should fall back to the first item.');
    }

    public function testRendersChipWidgetWithoutInputChrome(): void
    {
        $crawler = $this->renderTwigComponent(
            ChipDropdown::class,
            $this->baseProps([
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

        self::assertStringContainsString('ids-dropdown--chip', $this->getClassAttr($wrapper), 'Wrapper should include the chip modifier.');
        self::assertStringContainsString(
            'ids-dropdown--chip-small',
            $this->getClassAttr($wrapper),
            'Wrapper should include the chip size modifier.'
        );
        self::assertStringNotContainsString('ids-input', $this->getClassAttr($widget), 'Chip widget should not carry the input classes.');
        self::assertSame('0', $widget->attr('tabindex'), 'Chip widget should be focusable.');
        self::assertSame('false', $widget->attr('aria-expanded'), 'Chip widget should start collapsed.');
        self::assertSame(
            $widget->attr('aria-controls'),
            $crawler->filter('.ids-dropdown__items-container')->first()->attr('id'),
            'Chip widget should control the items panel.'
        );
        self::assertSame(1, $icon->count(), 'Chip should render the leading icon.');
        self::assertSame('true', $icon->attr('aria-hidden'), 'Chip icon should be decorative.');
        self::assertStringContainsString('ids-icon--tiny', $icon->html(), 'Small chip should render the tiny icon size.');
        self::assertSame('Language:', trim($label->text('')), 'Chip should render the static label.');
        self::assertSame(
            'Beta',
            trim($crawler->filter('.ids-dropdown__selection-info-items')->first()->text('')),
            'Chip should render the selected label.'
        );
        self::assertNull($wrapper->attr('icon'), 'The icon prop should be consumed, not rendered as an attribute.');
        self::assertNull($wrapper->attr('label'), 'The label prop should be consumed, not rendered as an attribute.');
    }

    public function testHidesSearchAndMarksItemsWithCheckIcon(): void
    {
        $items = array_map(
            static fn (int $index): array => ['id' => 'item-' . $index, 'label' => 'Item ' . $index],
            range(1, 12)
        );
        $crawler = $this->renderTwigComponent(ChipDropdown::class, $this->baseProps(['items' => $items, 'value' => 'item-2']))->crawler();

        self::assertNotNull(
            $crawler->filter('.ids-dropdown__search')->first()->attr('hidden'),
            'Chip should hide the search even above the visible items limit.'
        );
        self::assertCount(
            12,
            $crawler->filter('.ids-dropdown__items .ids-dropdown__item .ids-dropdown__item-check'),
            'Every item should carry the check icon for the selected state.'
        );
        self::assertCount(
            11,
            $crawler->filter('.ids-dropdown__items .ids-dropdown__item .ids-dropdown__item-check[hidden]'),
            'Only the selected item should show its check icon.'
        );
        self::assertStringContainsString(
            '#form-check',
            $crawler->filter('.ids-dropdown__items .ids-dropdown__item--selected .ids-dropdown__item-check use')->first()->attr('xlink:href') ?? '',
            'The check icon should be the form-check glyph.'
        );
        self::assertSame(
            'true',
            $crawler->filter('.ids-dropdown__items .ids-dropdown__item--selected .ids-dropdown__item-check')->first()->attr('aria-hidden'),
            'The check icon should be decorative.'
        );
        self::assertCount(
            1,
            $crawler->filter('.ids-dropdown__template .ids-dropdown__item .ids-dropdown__item-check'),
            'The item template should carry the check icon so items re-rendered by setItems() keep it.'
        );
    }

    public function testDisabledAddsModifierAndDisablesWidgetAndSelect(): void
    {
        $crawler = $this->renderTwigComponent(ChipDropdown::class, $this->baseProps(['disabled' => true]))->crawler();

        $widget = $this->getWidget($crawler);

        self::assertStringContainsString('ids-dropdown--disabled', $this->getClassAttr($this->getWrapper($crawler)), 'Wrapper should carry the disabled modifier.');
        self::assertSame('-1', $widget->attr('tabindex'), 'Disabled chip should leave the tab order.');
        self::assertSame('true', $widget->attr('aria-disabled'), 'Disabled chip should be announced as disabled.');
        self::assertNotNull($crawler->filter('.ids-dropdown__source > select')->first()->attr('disabled'), 'Source select should be disabled.');
    }

    public function testInvalidSizeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(ChipDropdown::class, $this->baseProps(['size' => 'large']));
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function baseProps(array $overrides = []): array
    {
        return array_replace([
            'name' => 'language',
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

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }
}
