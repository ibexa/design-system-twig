<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\AltRadio;

use Ibexa\DesignSystemTwig\Twig\Components\AltRadio\ListField;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ListFieldTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(ListField::class, $this->baseProps());

        self::assertInstanceOf(
            ListField::class,
            $component,
            'Component should mount as AltRadio\\ListField.'
        );
    }

    public function testDefaultRenderProducesWrapperAndItems(): void
    {
        $crawler = $this->renderTwigComponent(
            ListField::class,
            $this->baseProps()
        )->crawler();

        $wrapper = $this->getWrapper($crawler);
        $classes = $this->getClassAttr($wrapper);

        self::assertStringContainsString('ids-field', $classes, 'Wrapper should include "ids-field".');
        self::assertStringContainsString('ids-field--list', $classes, 'Wrapper should include "ids-field--list".');
        self::assertStringContainsString('ids-alt-radio-list-field', $classes, 'Wrapper should include alt radio modifier.');

        $items = $crawler->filter('.ids-choice-inputs-list__items .ids-alt-radio');
        self::assertSame(2, $items->count(), 'Should render exactly two alt radio items.');

        $firstTile = $this->getTile($items->eq(0));
        $secondTile = $this->getTile($items->eq(1));

        self::assertStringContainsString('Pick A', $this->getText($firstTile), 'First tile should render its label.');
        self::assertStringContainsString('Pick B', $this->getText($secondTile), 'Second tile should render its label.');
    }

    public function testPerItemTileClassAndDisabledFlagAreForwarded(): void
    {
        $props = $this->baseProps();
        $props['items'][0]['tileClass'] = 'is-featured';
        $props['items'][0]['disabled'] = true;

        $crawler = $this->renderTwigComponent(
            ListField::class,
            $props
        )->crawler();

        $items = $crawler->filter('.ids-choice-inputs-list__items .ids-alt-radio');
        $firstAltRadio = $items->eq(0);
        $firstTile = $this->getTile($firstAltRadio);

        self::assertStringContainsString(
            'is-featured',
            $this->getClassAttr($firstTile),
            'Custom tile class should be merged onto the tile element.'
        );

        $firstInput = $this->getAltRadioInput($firstAltRadio);
        self::assertNotNull(
            $firstInput->attr('disabled'),
            'Disabled=true on the item should render native "disabled" attribute.'
        );
    }

    public function testInvalidTileClassTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(ListField::class, $this->baseProps([
            'items' => [
                [
                    'id' => 'opt-a',
                    'value' => 'A',
                    'label' => 'Pick A',
                    'tileClass' => ['not-a-string'],
                ],
            ],
        ]));
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

    private function getAltRadioInput(Crawler $scope): Crawler
    {
        $node = $scope->filter('.ids-alt-radio__source > input')->first();
        self::assertGreaterThan(
            0,
            $node->count(),
            'Alt radio input should be present under ".ids-alt-radio__source > input".'
        );

        return $node;
    }

    private function getTile(Crawler $scope): Crawler
    {
        $node = $scope->filter('.ids-alt-radio__tile')->first();
        self::assertGreaterThan(0, $node->count(), 'Tile ".ids-alt-radio__tile" should be present.');

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
