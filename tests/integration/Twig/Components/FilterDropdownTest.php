<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\FilterDropdown;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class FilterDropdownTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(FilterDropdown::class, $this->baseProps(['value' => ['b']]));

        self::assertInstanceOf(FilterDropdown::class, $component, 'Component should mount as FilterDropdown.');
        self::assertSame(['b'], $component->value, '"value" prop should be set on the component.');
        self::assertSame(
            ['mode' => FilterDropdown::SUMMARY_COUNTER, 'text' => '1'],
            $component->getSummary(),
            'A default-type dropdown with one selection should summarise as a counter.'
        );
    }

    public function testRendersTriggerSelectAndCheckboxItems(): void
    {
        $crawler = $this->renderTwigComponent(FilterDropdown::class, $this->baseProps(['value' => ['b', 'c']]))->crawler();

        $wrapper = $this->getWrapper($crawler);
        $wrapperClass = (string) $wrapper->attr('class');
        self::assertStringContainsString('ids-dropdown--filter', $wrapperClass, 'Wrapper should carry the filter modifier.');
        self::assertStringContainsString('ids-dropdown--filter-default', $wrapperClass, 'Wrapper should carry the type modifier.');
        self::assertStringContainsString('ids-dropdown--selected', $wrapperClass, 'Wrapper should carry the selected modifier when a value is set.');

        $trigger = $wrapper->filter('.ids-dropdown__trigger')->first();
        self::assertGreaterThan(0, $trigger->count(), 'Trigger button should render.');
        self::assertSame('button', $trigger->attr('type'), 'Trigger should be a non-submitting button.');
        self::assertSame('false', $trigger->attr('aria-expanded'), 'Trigger should start collapsed.');
        self::assertSame('Filter', trim($trigger->filter('.ids-dropdown__trigger-label')->text('')), 'Trigger should show the label.');
        self::assertSame('2', trim($trigger->filter('.ids-dropdown__counter')->text('')), 'Counter should show the number of selected items.');
        self::assertNull($trigger->filter('.ids-dropdown__counter')->attr('hidden'), 'Counter should be visible with a selection.');
        self::assertGreaterThan(0, $trigger->filter('.ids-dropdown__chevron')->count(), 'Default type should render the chevron.');
        self::assertSame(0, $wrapper->filter('.ids-dropdown__widget')->count(), 'The input-like dropdown widget must not render for the filter.');

        $select = $wrapper->filter('.ids-dropdown__source select')->first();
        self::assertSame('multiple', $select->attr('multiple'), 'Source select should be multiple.');
        self::assertSame(2, $select->filter('option[selected]')->count(), 'Selected options should be marked.');

        self::assertSame(3, $wrapper->filter('.ids-dropdown__items input[type="checkbox"]')->count(), 'Every item should render a checkbox.');
        self::assertGreaterThan(0, $wrapper->filter('.ids-dropdown__footer .ids-dropdown__divider')->count(), 'Footer should render the divider.');

        $clearBtn = $wrapper->filter('.ids-dropdown__footer .ids-btn')->first();
        self::assertGreaterThan(0, $clearBtn->count(), 'Footer should render the Clear button.');
        self::assertNull($clearBtn->attr('disabled'), 'Clear should be enabled when items are selected.');
    }

    public function testDashboardTypeShowsTheSingleSelectedValue(): void
    {
        $crawler = $this->renderTwigComponent(FilterDropdown::class, $this->baseProps([
            'type' => FilterDropdown::TYPE_DASHBOARD,
            'value' => ['b'],
        ]))->crawler();

        $trigger = $this->getWrapper($crawler)->filter('.ids-dropdown__trigger')->first();
        self::assertSame('Filter:', trim($trigger->filter('.ids-dropdown__trigger-label')->text('')), 'Dashboard label should get the colon in value mode.');
        self::assertSame('Beta', trim($trigger->filter('.ids-dropdown__value')->text('')), 'Dashboard type should show the single selected label.');
        self::assertNotNull($trigger->filter('.ids-dropdown__counter')->attr('hidden'), 'Counter should be hidden in value mode.');
    }

    public function testIconOnlyTypeUsesAriaLabelAndEmptyStateDisablesClear(): void
    {
        $crawler = $this->renderTwigComponent(FilterDropdown::class, $this->baseProps([
            'type' => FilterDropdown::TYPE_MORE_FILTERS_SMALL,
            'hasSearch' => false,
            'disabled' => true,
        ]))->crawler();

        $wrapper = $this->getWrapper($crawler);
        $trigger = $wrapper->filter('.ids-dropdown__trigger')->first();
        self::assertSame('Filter', $trigger->attr('aria-label'), 'Icon-only trigger should expose the label as aria-label.');
        self::assertSame(0, $trigger->filter('.ids-dropdown__trigger-label')->count(), 'Icon-only trigger should not render the label text.');
        self::assertGreaterThan(0, $trigger->filter('.ids-dropdown__trigger-icon')->count(), 'Icon-only trigger should render the filters icon.');
        self::assertNotNull($trigger->attr('disabled'), 'Disabled prop should disable the trigger.');
        self::assertNotNull($wrapper->filter('.ids-dropdown__search')->attr('hidden'), 'hasSearch=false should hide the search.');
        self::assertNotNull($wrapper->filter('.ids-dropdown__footer .ids-btn')->attr('disabled'), 'Clear should be disabled without a selection.');
    }

    public function testInvalidTypeCausesResolverError(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(FilterDropdown::class, $this->baseProps(['type' => 'huge']));
    }

    public function testMissingLabelCausesResolverError(): void
    {
        $this->expectException(MissingOptionsException::class);

        $props = $this->baseProps();
        unset($props['label']);

        $this->mountTwigComponent(FilterDropdown::class, $props);
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function baseProps(array $overrides = []): array
    {
        return array_replace([
            'name' => 'filter',
            'label' => 'Filter',
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
}
