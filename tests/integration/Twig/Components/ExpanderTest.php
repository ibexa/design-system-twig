<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\Expander;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ExpanderTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            'ibexa:Expander',
            [
                'type' => 'caret',
                'expanded' => true,
                'expandLabel' => 'Show',
                'collapseLabel' => 'Hide',
                'hasIcon' => true,
                'message' => 'Toggle section',
            ]
        );

        self::assertInstanceOf(Expander::class, $component, 'Component should be instance of Expander');
        self::assertSame('caret', $component->type, 'Type should be "caret"');
        self::assertTrue($component->expanded, 'expanded should be true');
        self::assertSame('Show', $component->expandLabel, 'Expand label should be passed through');
        self::assertSame('Hide', $component->collapseLabel, 'Collapse label should be passed through');
        self::assertTrue($component->hasIcon, 'has_icon should be true');
        self::assertSame('arrow-caret-down', $component->iconName(), 'caret type should map to arrow-caret-down');
    }

    public function testRenderExpandedState(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:Expander',
            [
                'type' => 'caret',
                'expanded' => true,
                'expandLabel' => 'Show',
                'collapseLabel' => 'Hide',
                'hasIcon' => true,
                'message' => 'Click to hide section',
            ]
        );

        $crawler = $rendered->crawler();
        $button = $this->getButton($crawler);

        self::assertSame(1, $button->count(), 'There should be exactly one expander button');
        self::assertSame('button', $button->attr('type'), 'Expander should render as <button type="button">');

        $classAttr = (string) $button->attr('class');
        self::assertStringContainsString('ids-expander', $classAttr, 'Base expander class is missing');
        self::assertStringContainsString('ids-expander--has-label', $classAttr, 'has-label class is missing');
        self::assertStringContainsString('ids-expander--has-icon', $classAttr, 'has-icon class is missing');
        self::assertStringContainsString('ids-expander--is-expanded', $classAttr, 'is-expanded class is missing');

        self::assertSame('true', $button->attr('aria-expanded'), 'aria-expanded should be "true" in expanded state');
        self::assertSame('Hide', trim($button->filter('.ids-expander__label')->text()), 'Label should match collapse_label');

        $use = $button->filter('svg use')->first();
        self::assertSame(1, $use->count(), 'Icon <use> element should be present');
        $href = $use->attr('xlink:href');
        self::assertNotEmpty($href, 'Icon href should not be empty');
        self::assertStringEndsWith('#arrow-caret-down', $href, 'Caret type should map to arrow-caret-down');
    }

    public function testRenderCollapsedState(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:Expander',
            [
                'type' => 'caret',
                'expanded' => false,
                'expandLabel' => 'Show',
                'collapseLabel' => 'Hide',
                'hasIcon' => true,
                'message' => 'Click to expand',
            ]
        );

        $crawler = $rendered->crawler();
        $button = $this->getButton($crawler);

        $classAttr = (string) $button->attr('class');
        self::assertStringContainsString('ids-expander', $classAttr, 'Base class should be present');
        self::assertStringNotContainsString('ids-expander--is-expanded', $classAttr, 'is-expanded class should not be present');
        self::assertSame('false', $button->attr('aria-expanded'), 'aria-expanded should be "false" in collapsed state');
        self::assertSame('Show', trim($button->filter('.ids-expander__label')->text()), 'Label should match expand_label');
    }

    public function testChevronVariantRendersChevronIcon(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:Expander',
            [
                'type' => 'chevron',
                'expanded' => false,
                'expandLabel' => 'More',
                'collapseLabel' => 'Less',
                'hasIcon' => true,
                'message' => 'Toggle more info',
            ]
        );

        $crawler = $rendered->crawler();
        $use = $this->getButton($crawler)->filter('svg use')->first();

        self::assertSame(1, $use->count(), 'Icon should be rendered');
        $href = $use->attr('xlink:href');
        self::assertNotEmpty($href, 'Icon href should not be empty');
        self::assertStringEndsWith('#arrow-chevron-down', $href, 'Chevron type should map to arrow-chevron-down');
    }

    public function testNoIconWhenHasIconIsFalse(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:Expander',
            [
                'type' => 'caret',
                'expanded' => false,
                'expandLabel' => 'Show',
                'collapseLabel' => 'Hide',
                'hasIcon' => false,
                'message' => 'Just text',
            ]
        );

        $crawler = $rendered->crawler();
        $button = $this->getButton($crawler);

        $classAttr = (string) $button->attr('class');
        self::assertStringNotContainsString('ids-expander--has-icon', $classAttr, 'has-icon class should not be present');
        self::assertSame(0, $button->filter('svg use')->count(), 'Icon should not be rendered');
    }

    public function testDataAttributesReflectLabels(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:Expander',
            [
                'type' => 'caret',
                'expanded' => true,
                'expandLabel' => 'Open more',
                'collapseLabel' => 'Close',
                'hasIcon' => true,
                'message' => 'open-close',
            ]
        );

        $button = $this->getButton($rendered->crawler());
        self::assertSame('Open more', $button->attr('data-expand-label'), 'data-expand-label should match');
        self::assertSame('Close', $button->attr('data-collapse-label'), 'data-collapse-label should match');
    }

    public function testMergesCustomClassesFromAttributesIntoButton(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:Expander',
            [
                'type' => 'caret',
                'is_expanded' => false,
                'expand_label' => 'Show',
                'collapse_label' => 'Hide',
                'has_icon' => false,
                'message' => 'msg',
                'attributes' => [
                    'class' => 'u-mt-2 custom-hook',
                ],
            ]
        );

        $button = $this->getButton($rendered->crawler());
        $classAttr = (string) $button->attr('class');

        self::assertStringContainsString('ids-expander', $classAttr, 'Base class should be present');
        self::assertStringContainsString('u-mt-2', $classAttr, 'Custom class u-mt-2 should be merged');
        self::assertStringContainsString('custom-hook', $classAttr, 'Custom class custom-hook should be merged');
    }

    private function getButton(Crawler $crawler): Crawler
    {
        return $crawler->filter('button.ids-expander');
    }
}
