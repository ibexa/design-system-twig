<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\RadioButton;

use Ibexa\DesignSystemTwig\Twig\Components\RadioButton\ListField;
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

        self::assertInstanceOf(ListField::class, $component, 'Component should mount as RadioButton\\ListField.');
    }

    public function testDefaultRenderProducesWrapperAndRendersItems(): void
    {
        $crawler = $this->renderTwigComponent(
            ListField::class,
            $this->baseProps()
        )->crawler();

        $wrapper = $this->getWrapper($crawler);
        $classes = $this->getClassAttr($wrapper);

        self::assertStringContainsString('ids-field', $classes, 'Wrapper should include "ids-field" base class.');
        self::assertStringContainsString('ids-field--list', $classes, 'Wrapper should include "ids-field--list" modifier.');

        $items = $crawler->filter('.ids-choice-inputs-list__items .ids-radio-button-field');
        self::assertSame(2, $items->count(), 'Should render exactly two radio field items.');

        $firstInput = $this->getRadioInput($items->eq(0));
        $secondInput = $this->getRadioInput($items->eq(1));

        self::assertSame('radio', $firstInput->attr('type'), 'First item should be a radio input.');
        self::assertSame('group', $firstInput->attr('name'), 'First item "name" should be "group".');

        self::assertSame('radio', $secondInput->attr('type'), 'Second item should be a radio input.');
        self::assertSame('group', $secondInput->attr('name'), 'Second item "name" should be "group".');

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
        $classes = $this->getClassAttr($wrapper);

        self::assertStringContainsString('extra-class', $classes, 'Custom wrapper class should be merged.');
        self::assertSame('custom', $wrapper->attr('data-custom'), 'Custom data attribute should render on the wrapper.');
    }

    public function testPerItemPropsAreForwardedToNestedField(): void
    {
        $props = $this->baseProps();
        $props['items'][0]['disabled'] = true;

        $crawler = $this->renderTwigComponent(ListField::class, $props)->crawler();

        $items = $crawler->filter('.ids-choice-inputs-list__items .ids-radio-button-field');
        $firstInput = $this->getRadioInput($items->eq(0));
        $secondInput = $this->getRadioInput($items->eq(1));

        self::assertNotNull($firstInput->attr('disabled'), 'Disabled=true on item should render native "disabled" on the input.');
        self::assertNull($secondInput->attr('disabled'), 'Second item should not be disabled.');
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
                ['id' => 'opt-a', 'value' => 'A', 'label' => 'Pick A'],
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
                    'value' => 'A',
                    'label' => 'Pick A',
                ],
                [
                    'id' => 'opt-b',
                    'value' => 'B',
                    'label' => 'Pick B',
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

    private function getRadioInput(Crawler $scope): Crawler
    {
        $node = $scope->filter('.ids-radio-button > input')->first();
        self::assertGreaterThan(0, $node->count(), 'Radio input should be present under ".ids-radio-button > input".');

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
