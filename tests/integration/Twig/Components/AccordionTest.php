<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\Accordion;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class AccordionTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            'ibexa:Accordion',
            [
                'initiallyExpanded' => true,
            ]
        );

        self::assertInstanceOf(Accordion::class, $component);
        self::assertTrue($component->initiallyExpanded);
    }

    public function testDefaultRender(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:Accordion',
            [
                'initiallyExpanded' => true,
            ]
        );

        $crawler = $rendered->crawler();

        $this->testRootAccordionState($crawler);
        $this->testExpanderButton($crawler);
        $this->testVisibleLabelTextMatchesState($crawler);
        $this->testContentRegionExists($crawler);
        $this->testIconPresenceAndCorrectness($crawler);
    }

    private function testRootAccordionState(Crawler $crawler): void
    {
        self::assertGreaterThan(0, $crawler->filter('.ids-accordion')->count(), 'Accordion container is missing');
        self::assertStringContainsString(
            'ids-accordion--is-expanded',
            $crawler->filter('.ids-accordion')->attr('class') ?? '',
            'Accordion should have expanded state class'
        );
    }

    private function testExpanderButton(Crawler $crawler): void
    {
        $button = $this->getButton($crawler);

        self::assertSame(1, $button->count(), 'There should be exactly one expander button');
        self::assertSame('button', $button->attr('type'), 'Expander button should have type="button"');
    }

    private function testContentRegionExists(Crawler $crawler): void
    {
        self::assertSame(
            1,
            $crawler->filter('.ids-accordion__content')->count(),
            'The panel container is not present'
        );
    }

    private function testVisibleLabelTextMatchesState(Crawler $crawler): void
    {
        $button = $this->getButton($crawler);
        self::assertSame(
            'Hide',
            trim($button->filter('.ids-expander__label')->text()),
            'Expander label text should match expanded state ("Hide")'
        );
    }

    private function testIconPresenceAndCorrectness(Crawler $crawler): void
    {
        $button = $this->getButton($crawler);
        $selector = 'svg use';
        $use = $button->filter($selector)->first();
        self::assertSame(1, $use->count());

        $href = $use->attr('xlink:href');
        self::assertNotEmpty($href);
        self::assertStringEndsWith('#arrow-caret-down', $href);
    }

    private function getButton(Crawler $crawler): Crawler
    {
        return $crawler->filter('button.ids-expander');
    }
}
