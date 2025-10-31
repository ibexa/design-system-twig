<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\DropdownMulti;

use Ibexa\DesignSystemTwig\Twig\Components\DropdownMulti\Input;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class InputTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            Input::class,
            $this->baseProps(['value' => ['opt-a']])
        );

        self::assertInstanceOf(
            Input::class,
            $component,
            'Component should mount as DropdownMulti\\Input.'
        );
        self::assertSame(
            ['opt-a'],
            $component->value,
            '"value" prop should be set on the component.'
        );

        $selectedItems = $component->getSelectedItems();
        self::assertCount(
            1,
            $selectedItems,
            'getSelectedItems() should return one entry for selected ID.'
        );
        self::assertNotNull(
            $selectedItems[0],
            'Selected items array should contain the original item data.'
        );
        self::assertSame(
            'opt-a',
            $selectedItems[0]['id'],
            'Selected item should retain original ID.'
        );
        self::assertSame(
            'Pick A',
            $selectedItems[0]['label'],
            'Selected item should expose its label.'
        );
        self::assertFalse(
            $component->isEmpty(),
            'Component should not be empty when value contains ids.'
        );
    }

    public function testRenderProducesMultiSelectAndCheckboxItems(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, $this->baseProps([
            'value' => ['opt-b'],
        ]))->crawler();

        $wrapper = $this->getDropdownWrapper($crawler);
        $wrapperClass = $this->getClassAttr($wrapper);

        self::assertStringContainsString(
            'ids-dropdown--multi',
            $wrapperClass,
            'Wrapper should include multi modifier class.'
        );

        $select = $this->getSelectElement($wrapper);
        self::assertSame(
            'group',
            $select->attr('name'),
            'Select name should match provided name.'
        );
        self::assertSame(
            'multiple',
            $select->attr('multiple'),
            'Select should render with "multiple" attribute.'
        );

        $options = $select->filter('option');
        self::assertSame(
            2,
            $options->count(),
            'Select should render one option per item.'
        );
        self::assertSame(
            'opt-a',
            $options->eq(0)->attr('value'),
            'First option should expose the first item id.'
        );
        self::assertNull(
            $options->eq(0)->attr('selected'),
            'First option should not be selected.'
        );
        self::assertSame(
            'opt-b',
            $options->eq(1)->attr('value'),
            'Second option should expose the second item id.'
        );
        self::assertNotNull(
            $options->eq(1)->attr('selected'),
            'Second option should be marked as selected.'
        );

        $checkboxes = $wrapper->filter('.ids-dropdown__items input[type="checkbox"]');
        self::assertSame(
            2,
            $checkboxes->count(),
            'Dropdown list should include a checkbox per item.'
        );
        self::assertSame(
            'group-checkbox',
            $checkboxes->eq(0)->attr('name'),
            'Checkbox name should derive from component name.'
        );
        self::assertNotNull(
            $checkboxes->eq(1)->attr('checked'),
            'Checkbox representing the selected option should be checked.'
        );
        self::assertSame(
            'true',
            $checkboxes->eq(0)->attr('data-ids-custom-init'),
            'Checkbox should opt-in for custom JS init.'
        );
    }

    public function testSelectionInfoDisplaysSelectedLabels(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, $this->baseProps([
            'value' => ['opt-a', 'opt-b'],
        ]))->crawler();

        $selectionInfo = $crawler
            ->filter('.ids-dropdown__selection-info-items')
            ->first();
        self::assertGreaterThan(
            0,
            $selectionInfo->count(),
            'Selection info container should render.'
        );
        $selectionText = preg_replace('/\s+/', ' ', $selectionInfo->text(''));
        self::assertIsString($selectionText, 'Selection text normalisation should yield a string.');
        $selectionText = trim($selectionText);
        self::assertSame(
            'Pick A, Pick B',
            $selectionText,
            'Selection info should list chosen labels.'
        );
        self::assertNull(
            $selectionInfo->attr('hidden'),
            'Selection info should be visible when there are selections.'
        );

        $placeholder = $crawler->filter('.ids-dropdown__placeholder')->first();
        self::assertGreaterThan(
            0,
            $placeholder->count(),
            'Placeholder container should render.'
        );
        self::assertNotNull(
            $placeholder->attr('hidden'),
            'Placeholder should be hidden when selections are present.'
        );
    }

    public function testEmptyValueShowsPlaceholder(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, $this->baseProps([
            'value' => [],
        ]))->crawler();

        $placeholder = $crawler->filter('.ids-dropdown__placeholder')->first();
        self::assertGreaterThan(
            0,
            $placeholder->count(),
            'Placeholder container should render.'
        );
        self::assertNull(
            $placeholder->attr('hidden'),
            'Placeholder should be visible when there are no selections.'
        );

        $selectionInfo = $crawler
            ->filter('.ids-dropdown__selection-info-items')
            ->first();
        self::assertNotNull(
            $selectionInfo->attr('hidden'),
            'Selection info should be hidden when there are no selections.'
        );
    }

    public function testInvalidValueTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(
            Input::class,
            $this->baseProps(['value' => 'opt-a'])
        );
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
                ['id' => 'opt-a', 'label' => 'Pick A'],
                ['id' => 'opt-b', 'label' => 'Pick B'],
            ],
            'value' => [],
        ], $overrides);
    }

    private function getDropdownWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-dropdown')->first();
        self::assertGreaterThan(0, $node->count(), 'Dropdown wrapper should be present.');

        return $node;
    }

    private function getSelectElement(Crawler $scope): Crawler
    {
        $node = $scope->filter('.ids-dropdown__source select')->first();
        self::assertGreaterThan(0, $node->count(), 'Native select element should be present.');

        return $node;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }
}
