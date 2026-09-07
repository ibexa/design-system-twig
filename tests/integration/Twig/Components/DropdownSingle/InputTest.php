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

    public function testGroupedItemsRenderGroupMarkup(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, $this->baseProps([
            'value' => 'banana',
            'items' => [
                ['id' => 'a', 'label' => 'Alpha'],
                [
                    'id' => 'fruits',
                    'label' => 'Fruits',
                    'items' => [
                        ['id' => 'apple', 'label' => 'Apple'],
                        ['id' => 'banana', 'label' => 'Banana'],
                    ],
                ],
            ],
        ]))->crawler();

        $groups = $crawler->filter('.ids-dropdown__items > .ids-dropdown__group');
        self::assertSame(1, $groups->count(), 'One group node should be rendered for the grouped entry.');

        $group = $groups->first();
        $groupLabel = $group->filter('.ids-dropdown__group-label')->first();
        self::assertSame('group', $group->attr('role'), 'Group node should carry role="group".');
        self::assertSame('fruits', $groupLabel->attr('id'), 'Group label id should come from the group id.');
        self::assertSame($groupLabel->attr('id'), $group->attr('aria-labelledby'), 'Group should be labelled by its label node.');
        self::assertSame('Fruits', trim($groupLabel->text('')), 'Group label should render the group label.');
        self::assertNull($groupLabel->attr('tabindex'), 'Group label must not be focusable.');
        self::assertSame(
            2,
            $group->filter('.ids-dropdown__group-items > .ids-dropdown__item')->count(),
            'Grouped items should be nested inside the group items list.'
        );
        self::assertSame(
            1,
            $crawler->filter('.ids-dropdown__items > .ids-dropdown__item')->count(),
            'Ungrouped items should stay direct children of the items list.'
        );

        $select = $this->getSelect($crawler);
        self::assertSame(2, $select->filter('optgroup[label="Fruits"] > option')->count(), 'Source select should render an optgroup with the grouped options.');
        self::assertSame('banana', $select->filter('option[selected]')->attr('value'), 'Selected option inside a group should be marked selected.');
        self::assertSame(
            'Banana',
            trim($crawler->filter('.ids-dropdown__selection-info-items')->first()->text('')),
            'Selected label should resolve for an item inside a group.'
        );

        $noResults = $crawler->filter('.ids-dropdown__no-results')->first();
        self::assertGreaterThan(0, $noResults->count(), 'No-results node should be rendered.');
        self::assertNotNull($noResults->attr('hidden'), 'No-results node should start hidden.');
    }

    public function testSearchVisibilityCountsLeafItems(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, $this->baseProps([
            'maxVisibleItems' => 2,
            'items' => [
                ['id' => 'a', 'label' => 'Alpha'],
                [
                    'id' => 'fruits',
                    'label' => 'Fruits',
                    'items' => [
                        ['id' => 'apple', 'label' => 'Apple'],
                        ['id' => 'banana', 'label' => 'Banana'],
                    ],
                ],
            ],
        ]))->crawler();

        self::assertNull(
            $crawler->filter('.ids-dropdown__search')->first()->attr('hidden'),
            'Three leaf items over a limit of two should make the search visible even though there are only two top-level entries.'
        );
    }

    public function testGroupMissingLabelCausesResolverError(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->mountTwigComponent(Input::class, $this->baseProps([
            'items' => [['id' => 'g', 'items' => [['id' => 'a', 'label' => 'Alpha']]]],
        ]));
    }

    public function testNestedGroupCausesResolverError(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Input::class, $this->baseProps([
            'items' => [['label' => 'Outer', 'items' => [['label' => 'Inner', 'items' => [['id' => 'a', 'label' => 'Alpha']]]]]],
        ]));
    }

    public function testEmptyGroupIsDropped(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, $this->baseProps([
            'items' => [
                ['label' => 'Empty', 'items' => []],
                ['id' => 'a', 'label' => 'Alpha'],
            ],
        ]))->crawler();

        self::assertSame(0, $crawler->filter('.ids-dropdown__items > .ids-dropdown__group')->count(), 'A group without items should not be rendered.');
        self::assertSame(1, $this->getSelect($crawler)->filter('option')->count(), 'Only the ungrouped option should remain in the source select.');
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
