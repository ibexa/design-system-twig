<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\Button;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ButtonTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            'ibexa:button',
            [
                'size' => 'small',
                'type' => 'secondary',
                'disabled' => true,
                'icon' => 'arrow-right',
            ]
        );

        self::assertInstanceOf(Button::class, $component);
        self::assertSame('small', $component->size, 'Size should be resolved to "small"');
        self::assertSame('secondary', $component->type, 'Type should be resolved to "secondary"');
        self::assertTrue($component->disabled, 'Disabled should be true');
        self::assertSame('arrow-right', $component->icon, 'Icon name should be passed through');
        self::assertSame('tiny-small', $component->iconSize(), 'iconSize() should map "small" to "tiny-small"');
    }

    public function testDefaultRender(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', []);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);

        self::assertSame(1, $button->count(), 'There should be exactly one button');
        self::assertSame('button', $button->attr('type'), 'Button should have type="button" by default');

        $classAttr = (string) $button->attr('class');
        self::assertStringContainsString('ids-btn', $classAttr, 'Base class is missing');
        self::assertStringContainsString('ids-btn--primary', $classAttr, 'Default type should be "primary"');
        self::assertStringContainsString('ids-btn--medium', $classAttr, 'Default size should be "medium"');
        self::assertStringNotContainsString('ids-btn--disabled', $classAttr, 'Disabled class should not be present by default');

        self::assertSame(1, $button->filter('.ids-btn__label')->count(), 'Label container should be present');

        self::assertSame(0, $button->filter('.ids-btn__icon')->count(), 'Icon container should not be present without icon');
    }

    public function testDisabledStateRender(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'disabled' => true,
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);
        $classAttr = (string) $button->attr('class');

        self::assertStringContainsString('ids-btn--disabled', $classAttr, 'Disabled class should be present');
        self::assertNotNull($button->attr('disabled'), 'Disabled attribute should be present');
    }

    public function testVariantAndSizeClasses(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'type' => 'secondary-alt',
            'size' => 'small',
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);
        $classAttr = (string) $button->attr('class');

        self::assertStringContainsString('ids-btn--secondary-alt', $classAttr, 'Type variant class should be applied');
        self::assertStringContainsString('ids-btn--small', $classAttr, 'Size class should be applied');
    }

    public function testMergesCustomClassesFromAttributes(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'attributes' => [
                'class' => 'u-ml-2 custom-hook',
                'data-test' => 'button-x',
            ],
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);
        $classAttr = (string) $button->attr('class');

        self::assertStringContainsString('ids-btn', $classAttr);
        self::assertStringContainsString('u-ml-2', $classAttr, 'Custom class should be merged into class attribute');
        self::assertStringContainsString('custom-hook', $classAttr, 'Custom class should be merged into class attribute');
        self::assertSame('button', $button->attr('type'), 'Default type attribute remains "button"');
        self::assertSame('button-x', $button->attr('data-test'), 'Arbitrary attributes should be preserved');
    }

    public function testIconRenderingIsConditional(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'icon' => 'arrow-right',
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);

        self::assertSame(1, $button->filter('.ids-btn__icon')->count(), 'Icon container should be present when icon is set');
    }

    private function getButton(Crawler $crawler): Crawler
    {
        return $crawler->filter('button.ids-btn');
    }
}
