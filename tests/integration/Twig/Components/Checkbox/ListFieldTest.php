<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\Checkbox;

use Ibexa\DesignSystemTwig\Twig\Components\Checkbox\ListField;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ListFieldTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(ListField::class, $this->baseProps());
        self::assertInstanceOf(ListField::class, $component, 'Component should mount as Checkbox\\ListField.');
    }

    public function testDefaultRenderProducesWrapperAndRendersItems(): void
    {
        $crawler = $this->renderTwigComponent(ListField::class, $this->baseProps())->crawler();

        $wrapper = $this->getWrapper($crawler);
        $classes = $this->getClassAttr($wrapper);
        self::assertStringContainsString('ids-field', $classes, 'Wrapper should include "ids-field".');
        self::assertStringContainsString('ids-field--list', $classes, 'Wrapper should include "ids-field--list".');
        self::assertStringContainsString('ids-choice-inputs-list', $classes, 'Wrapper should include "ids-choice-inputs-list".');
        self::assertStringContainsString('ids-checkboxes-list-field', $classes, 'Wrapper should include "ids-checkboxes-list-field".');

        $items = $crawler->filter('.ids-choice-inputs-list__items .ids-checkbox-field');
        self::assertSame(2, $items->count(), 'Should render exactly two checkbox field items.');

        $firstInput = $this->getCheckboxInput($items->eq(0));
        $secondInput = $this->getCheckboxInput($items->eq(1));

        self::assertSame('checkbox', $firstInput->attr('type'), 'First item should be a checkbox input.');
        self::assertSame('group', $firstInput->attr('name'), 'First item "name" should be taken from the top-level group.');
        self::assertSame('checkbox', $secondInput->attr('type'), 'Second item should be a checkbox input.');
        self::assertSame('group', $secondInput->attr('name'), 'Second item "name" should be taken from the top-level group.');

        self::assertStringContainsString('Pick A', $this->getText($items->eq(0)), 'First item should render its label.');
        self::assertStringContainsString('Pick B', $this->getText($items->eq(1)), 'Second item should render its label.');
    }

    public function testWrapperAttributesMergeClassAndData(): void
    {
        $crawler = $this->renderTwigComponent(
            ListField::class,
            $this->baseProps([
                'attributes' => ['class' => 'extra-class', 'data-custom' => 'custom'],
            ])
        )->crawler();

        $wrapper = $this->getWrapper($crawler);
        self::assertStringContainsString('extra-class', $this->getClassAttr($wrapper), 'Custom wrapper class should be merged.');
        self::assertSame('custom', $wrapper->attr('data-custom'), 'Custom data attribute should render on the wrapper.');
    }

    public function testDirectionVariantAddsExpectedClass(): void
    {
        $crawler = $this->renderTwigComponent(
            ListField::class,
            $this->baseProps(['direction' => 'horizontal'])
        )->crawler();

        $wrapper = $this->getWrapper($crawler);

        self::assertStringContainsString('ids-choice-inputs-list--horizontal', $this->getClassAttr($wrapper), 'Direction "horizontal" should add the corresponding class.');
    }

    public function testPerItemPropsAreForwardedToNestedField(): void
    {
        $props = $this->baseProps();
        $props['items'][0]['disabled'] = true;
        $props['items'][1]['required'] = true;

        $crawler = $this->renderTwigComponent(
            ListField::class,
            $props
        )->crawler();

        $items = $crawler->filter('.ids-choice-inputs-list__items .ids-checkbox-field');
        $first = $this->getCheckboxInput($items->eq(0));
        $second = $this->getCheckboxInput($items->eq(1));

        self::assertNotNull($first->attr('disabled'), 'Disabled=true on first item should render native "disabled".');
        self::assertNull($first->attr('required'), 'First item should not be required.');

        self::assertNull($second->attr('disabled'), 'Second item should not be disabled.');
        self::assertNotNull($second->attr('required'), 'Required=true on second item should render native "required".');
    }

    public function testInvalidItemsTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(ListField::class, [
            'name' => 'group',
            'items' => 'oops',
        ]);
    }

    public function testMissingRequiredOptionsCauseResolverErrorOnMount(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->mountTwigComponent(ListField::class, [
            'items' => [
                ['id' => 'opt-a', 'label' => 'Pick A'],
            ],
        ]);
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
                [
                    'id' => 'opt-a',
                    'label' => 'Pick A',
                    'value' => 'A',
                ],
                [
                    'id' => 'opt-b',
                    'label' => 'Pick B',
                    'value' => 'B',
                ],
            ],
        ], $overrides);
    }

    private function getWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-field')->first();
        self::assertGreaterThan(0, $node->count(), 'Wrapper ".ids-field" should be present.');

        return $node;
    }

    private function getCheckboxInput(Crawler $scope): Crawler
    {
        $node = $scope->filter('input[type="checkbox"]')->first();
        self::assertGreaterThan(0, $node->count(), 'Checkbox input should be present.');

        return $node;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }

    private function getText(Crawler $node): string
    {
        return trim($node->text(''));
    }
}
