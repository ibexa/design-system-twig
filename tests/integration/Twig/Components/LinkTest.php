<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\Link;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class LinkTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            'ibexa:link',
            [
                'href' => '/dashboard',
                'variant' => 'button',
                'size' => 'small',
                'type' => 'secondary',
                'icon' => 'arrow-right',
            ]
        );

        self::assertInstanceOf(Link::class, $component);
        self::assertSame('/dashboard', $component->href, 'Href should be resolved');
        self::assertSame('button', $component->variant, 'Variant should be resolved to "button"');
        self::assertSame('small', $component->size, 'Size should be resolved to "small"');
        self::assertSame('secondary', $component->type, 'Type should be resolved to "secondary"');
        self::assertSame('arrow-right', $component->icon, 'Icon name should be passed through');
        self::assertSame('tiny-small', $component->iconSize(), 'iconSize() should map "small" to "tiny-small"');
    }

    public function testHrefIsRequired(): void
    {
        $this->expectException(\Symfony\Component\OptionsResolver\Exception\MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "href" is missing');

        $this->mountTwigComponent('ibexa:link', []);
    }

    public function testDefaultButtonVariantRender(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:link', [
            'href' => '/dashboard',
        ]);
        $crawler = $rendered->crawler();

        $link = $this->getButtonLink($crawler);

        self::assertSame(1, $link->count(), 'There should be exactly one button-styled link');
        self::assertSame('/dashboard', $link->attr('href'), 'Link should have correct href');

        $classAttr = (string) $link->attr('class');
        self::assertStringContainsString('ids-btn', $classAttr, 'Base button class is missing');
        self::assertStringContainsString('ids-btn--tertiary', $classAttr, 'Default type should be "tertiary"');
        self::assertStringContainsString('ids-btn--medium', $classAttr, 'Default size should be "medium"');
        self::assertStringNotContainsString('ids-btn--disabled', $classAttr, 'Disabled class should not be present');

        self::assertSame(1, $link->filter('.ids-btn__label')->count(), 'Label container should be present');
        self::assertSame(0, $link->filter('.ids-btn__icon')->count(), 'Icon container should not be present without icon');
    }

    public function testButtonVariantWithAllTypes(): void
    {
        $types = ['primary', 'secondary', 'tertiary', 'secondary-alt', 'tertiary-alt'];

        foreach ($types as $type) {
            $rendered = $this->renderTwigComponent('ibexa:link', [
                'href' => '/test',
                'variant' => 'button',
                'type' => $type,
            ]);
            $crawler = $rendered->crawler();

            $link = $this->getButtonLink($crawler);
            $classAttr = (string) $link->attr('class');

            self::assertStringContainsString("ids-btn--{$type}", $classAttr, "Type variant class should be applied for {$type}");
        }
    }

    public function testButtonVariantSizes(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:link', [
            'href' => '/test',
            'variant' => 'button',
            'type' => 'secondary-alt',
            'size' => 'small',
        ]);
        $crawler = $rendered->crawler();

        $link = $this->getButtonLink($crawler);
        $classAttr = (string) $link->attr('class');

        self::assertStringContainsString('ids-btn--secondary-alt', $classAttr, 'Type variant class should be applied');
        self::assertStringContainsString('ids-btn--small', $classAttr, 'Size class should be applied');
    }

    public function testButtonVariantDisabledState(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:link', [
            'href' => '/test',
            'variant' => 'button',
            'disabled' => true,
        ]);
        $crawler = $rendered->crawler();

        $link = $this->getButtonLink($crawler);
        $classAttr = (string) $link->attr('class');

        self::assertStringNotContainsString('ids-btn--disabled', $classAttr, 'Disabled class should NOT be present');
        self::assertSame('true', $link->attr('aria-disabled'), 'aria-disabled attribute should be present and set to true');
    }

    public function testButtonVariantWithIcon(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:link', [
            'href' => '/test',
            'variant' => 'button',
            'icon' => 'arrow-right',
        ]);
        $crawler = $rendered->crawler();

        $link = $this->getButtonLink($crawler);

        self::assertSame(1, $link->filter('.ids-btn__icon')->count(), 'Icon container should be present when icon is set');
    }

    public function testButtonVariantWithIconUrl(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:link', [
            'href' => '/test',
            'variant' => 'button',
            'icon_url' => '/assets/icons.svg#calendar-schedule',
        ]);
        $crawler = $rendered->crawler();

        $link = $this->getButtonLink($crawler);

        self::assertSame(1, $link->filter('.ids-btn__icon')->count(), 'Icon container should be present when icon_url is set');
        self::assertSame('/assets/icons.svg#calendar-schedule', $link->filter('.ids-btn__icon use')->attr('xlink:href'));
    }

    public function testIconAndIconUrlAreMutuallyExclusive(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage("Options 'icon' and 'icon_url' cannot be used together.");

        $this->mountTwigComponent('ibexa:link', [
            'href' => '/test',
            'icon' => 'arrow-right',
            'icon_url' => '/assets/icons.svg#calendar-schedule',
        ]);
    }

    public function testButtonVariantIconOnly(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:link', [
            'href' => '/edit',
            'variant' => 'button',
            'icon' => 'edit',
        ]);
        $crawler = $rendered->crawler();

        $link = $this->getButtonLink($crawler);
        $classAttr = (string) $link->attr('class');

        self::assertStringContainsString('ids-btn--icon-only', $classAttr, 'Icon-only class should be present');
        self::assertSame(1, $link->filter('.ids-btn__icon')->count(), 'Icon container should be present');
        self::assertSame(0, $link->filter('.ids-btn__label')->count(), 'Label container should not be present for icon-only');
    }

    public function testButtonVariantMergesCustomClasses(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:link', [
            'href' => '/test',
            'variant' => 'button',
            'attributes' => [
                'class' => 'u-ml-2 custom-hook',
                'data-test' => 'link-x',
            ],
        ]);
        $crawler = $rendered->crawler();

        $link = $this->getButtonLink($crawler);
        $classAttr = (string) $link->attr('class');

        self::assertStringContainsString('ids-btn', $classAttr);
        self::assertStringContainsString('u-ml-2', $classAttr, 'Custom class should be merged into class attribute');
        self::assertStringContainsString('custom-hook', $classAttr, 'Custom class should be merged into class attribute');
        self::assertSame('link-x', $link->attr('data-test'), 'Arbitrary attributes should be preserved');
    }

    public function testTextVariantRender(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:link', [
            'href' => 'https://example.com',
            'variant' => 'text',
        ]);
        $crawler = $rendered->crawler();

        $link = $this->getTextLink($crawler);

        self::assertSame(1, $link->count(), 'There should be exactly one text link');
        self::assertSame('https://example.com', $link->attr('href'), 'Link should have correct href');

        $classAttr = (string) $link->attr('class');
        self::assertStringContainsString('ids-link', $classAttr, 'Text link class should be present');
        self::assertStringNotContainsString('ids-btn', $classAttr, 'Button classes should not be present');
    }

    public function testTextVariantIgnoresButtonProps(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:link', [
            'href' => 'https://example.com',
            'variant' => 'text',
            'type' => 'primary',
            'size' => 'small',
            'icon' => 'arrow-right',
        ]);
        $crawler = $rendered->crawler();

        $link = $this->getTextLink($crawler);
        $classAttr = (string) $link->attr('class');

        self::assertStringContainsString('ids-link', $classAttr, 'Text link class should be present');
        self::assertStringNotContainsString('ids-btn', $classAttr, 'Button classes should not be present');
        self::assertStringNotContainsString('primary', $classAttr, 'Type classes should not be present');
        self::assertStringNotContainsString('small', $classAttr, 'Size classes should not be present');
        self::assertSame(0, $crawler->filter('.ids-btn__icon')->count(), 'Icon should not be rendered in text variant');
    }

    public function testTextVariantWithCustomClasses(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:link', [
            'href' => 'https://example.com',
            'variant' => 'text',
            'attributes' => [
                'class' => 'custom-text-link',
                'target' => '_blank',
                'rel' => 'noopener noreferrer',
            ],
        ]);
        $crawler = $rendered->crawler();

        $link = $this->getTextLink($crawler);
        $classAttr = (string) $link->attr('class');

        self::assertStringContainsString('ids-link', $classAttr);
        self::assertStringContainsString('custom-text-link', $classAttr, 'Custom class should be merged');
        self::assertSame('_blank', $link->attr('target'), 'Target attribute should be preserved');
        self::assertSame('noopener noreferrer', $link->attr('rel'), 'Rel attribute should be preserved');
    }

    private function getButtonLink(Crawler $crawler): Crawler
    {
        return $crawler->filter('a.ids-btn');
    }

    private function getTextLink(Crawler $crawler): Crawler
    {
        return $crawler->filter('a.ids-link');
    }
}
