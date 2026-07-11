<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\Switcher;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class SwitcherTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function defaultItems(): array
    {
        return [
            ['value' => 'list', 'label' => 'List'],
            ['value' => 'grid', 'label' => 'Grid'],
            ['value' => 'tree', 'label' => 'Tree'],
        ];
    }

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Switcher::class, [
            'items' => self::defaultItems(),
            'selectedValue' => 'list',
            'size' => 'small',
            'type' => 'builders',
            'overflow' => true,
            'moreLabel' => 'More',
            'name' => 'view-switcher',
        ]);

        self::assertInstanceOf(Switcher::class, $component, 'Component should mount as Switcher.');
    }

    public function testRendersRadiogroupWithOneItemPerEntry(): void
    {
        $crawler = $this->render(['items' => self::defaultItems(), 'selectedValue' => 'list']);
        $root = $this->getRoot($crawler);

        self::assertStringContainsString('ids-switcher', $this->getClassAttr($root), 'Root should carry the base class.');
        self::assertSame('radiogroup', $root->attr('role'), 'Root should have role="radiogroup".');
        self::assertCount(3, $crawler->filter('.ids-switcher__item'), 'One .ids-switcher__item per entry should render.');
        self::assertCount(3, $crawler->filter('.ids-switcher__item[role="radio"]'), 'Each item should be role="radio".');
        self::assertCount(3, $crawler->filter('.ids-switcher__item-label'), 'Each item should carry a label element.');
    }

    public function testDefaultSizeAndTypeModifiers(): void
    {
        $crawler = $this->render(['items' => self::defaultItems()]);
        $class = $this->getClassAttr($this->getRoot($crawler));

        self::assertStringContainsString('ids-switcher--large', $class, 'Default size modifier should be large.');
        self::assertStringContainsString('ids-switcher--backoffice', $class, 'Default type modifier should be backoffice.');
    }

    public function testSizeSmallModifier(): void
    {
        $crawler = $this->render(['items' => self::defaultItems(), 'size' => 'small']);

        self::assertStringContainsString('ids-switcher--small', $this->getClassAttr($this->getRoot($crawler)), 'size=small should add the small modifier.');
    }

    public function testTypeBuildersModifier(): void
    {
        $crawler = $this->render(['items' => self::defaultItems(), 'type' => 'builders']);

        self::assertStringContainsString('ids-switcher--builders', $this->getClassAttr($this->getRoot($crawler)), 'type=builders should add the builders modifier.');
    }

    public function testSelectedItemGetsSelectedModifierAndAriaChecked(): void
    {
        $crawler = $this->render(['items' => self::defaultItems(), 'selectedValue' => 'grid']);
        $selected = $crawler->filter('.ids-switcher__item[data-value="grid"]')->first();

        self::assertStringContainsString('ids-switcher__item--selected', (string) $selected->attr('class'), 'Selected item should carry the --selected modifier.');
        self::assertSame('true', $selected->attr('aria-checked'), 'Selected item should have aria-checked="true".');

        $unselected = $crawler->filter('.ids-switcher__item[data-value="list"]')->first();

        self::assertStringNotContainsString('ids-switcher__item--selected', (string) $unselected->attr('class'), 'Unselected item should not carry the --selected modifier.');
        self::assertSame('false', $unselected->attr('aria-checked'), 'Unselected item should have aria-checked="false".');
    }

    public function testDisabledItemGetsModifierAndDisabledAttribute(): void
    {
        $crawler = $this->render([
            'items' => [
                ['value' => 'list', 'label' => 'List'],
                ['value' => 'grid', 'label' => 'Grid', 'disabled' => true],
            ],
            'selectedValue' => 'list',
        ]);
        $disabled = $crawler->filter('.ids-switcher__item[data-value="grid"]')->first();

        self::assertStringContainsString('ids-switcher__item--disabled', (string) $disabled->attr('class'), 'Disabled item should carry the --disabled modifier.');
        self::assertNotNull($disabled->attr('disabled'), 'Disabled item should have the native disabled attribute.');
    }

    public function testErrorItemGetsModifierAndIcon(): void
    {
        $crawler = $this->render([
            'items' => [
                ['value' => 'list', 'label' => 'List'],
                ['value' => 'grid', 'label' => 'Grid', 'error' => true],
            ],
            'selectedValue' => 'list',
        ]);
        $errorItem = $crawler->filter('.ids-switcher__item[data-value="grid"]')->first();

        self::assertStringContainsString('ids-switcher__item--error', (string) $errorItem->attr('class'), 'Error item should carry the --error modifier.');
        self::assertGreaterThan(0, $errorItem->filter('.ids-switcher__item-icon .ids-icon')->count(), 'Error item should render an icon inside .ids-switcher__item-icon.');
    }

    public function testActiveValueFallsBackToFirstEnabledWhenSelectionDisabled(): void
    {
        $crawler = $this->render([
            'items' => [
                ['value' => 'list', 'label' => 'List', 'disabled' => true],
                ['value' => 'grid', 'label' => 'Grid'],
            ],
            'selectedValue' => 'list',
        ]);

        $grid = $crawler->filter('.ids-switcher__item[data-value="grid"]')->first();

        self::assertSame('0', $grid->attr('tabindex'), 'When the selected item is disabled, the first enabled item owns the tab stop.');
    }

    public function testOverflowRendersModifierAndMoreTrigger(): void
    {
        $crawler = $this->render(['items' => self::defaultItems(), 'selectedValue' => 'list', 'overflow' => true]);

        self::assertStringContainsString('ids-switcher--overflow', $this->getClassAttr($this->getRoot($crawler)), 'overflow=true should add the --overflow modifier.');

        $more = $crawler->filter('.ids-switcher__item--more')->first();

        self::assertGreaterThan(0, $more->count(), 'overflow=true should render the More trigger.');
        self::assertStringContainsString('More', trim($more->text('')), 'More trigger should render its label.');
        self::assertGreaterThan(0, $more->filter('.ids-switcher__item-icon .ids-icon')->count(), 'More trigger should render the double-arrow icon.');
    }

    public function testNoMoreTriggerWithoutOverflow(): void
    {
        $crawler = $this->render(['items' => self::defaultItems(), 'selectedValue' => 'list']);

        self::assertCount(0, $crawler->filter('.ids-switcher__item--more'), 'Non-overflow switcher should not render a More trigger.');
    }

    public function testAttributesMergeClass(): void
    {
        $crawler = $this->render(['items' => self::defaultItems(), 'attributes' => ['class' => 'extra-class']]);

        self::assertStringContainsString('extra-class', $this->getClassAttr($this->getRoot($crawler)), 'Custom class should be merged into the root class attribute.');
    }

    public function testInvalidSizeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Switcher::class, ['items' => self::defaultItems(), 'size' => 'giant']);
    }

    public function testInvalidTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Switcher::class, ['items' => self::defaultItems(), 'type' => 'frontoffice']);
    }

    public function testInvalidOverflowTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Switcher::class, ['items' => self::defaultItems(), 'overflow' => 'yes']);
    }

    /**
     * @param array<string, mixed> $props
     */
    private function render(array $props): Crawler
    {
        return $this->renderTwigComponent(Switcher::class, $props)->crawler();
    }

    private function getRoot(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('div.ids-switcher')->first();
        self::assertGreaterThan(0, $node->count(), 'Switcher root ".ids-switcher" should be present.');

        return $node;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }
}
