<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Generator;
use Ibexa\DesignSystemTwig\Twig\Components\IconButton;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class IconButtonTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent('ibexa:IconButton', [
            'type' => 'secondary',
            'size' => 'small',
            'icon' => 'arrow-right',
            'disabled' => false,
        ]);

        self::assertInstanceOf(IconButton::class, $component, 'Component should be instance of IconButton');
    }

    /**
     * @dataProvider typeAndSizeProvider
     *
     * @param array<string,mixed> $props
     * @param list<string> $expectedClasses
     */
    public function testRenderAppliesVariantAndSizeClasses(array $props, array $expectedClasses): void
    {
        $rendered = $this->renderTwigComponent('ibexa:IconButton', $props + ['icon' => 'arrow-right']);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);
        self::assertSame(1, $button->count(), 'There should be exactly one IconButton');
        self::assertSame('button', $button->attr('type'), 'Rendered element should be <button type="button">');

        $classAttr = (string) $button->attr('class');
        self::assertStringContainsString('ids-btn', $classAttr, 'Base button class should be present');
        foreach ($expectedClasses as $cls) {
            self::assertStringContainsString($cls, $classAttr, sprintf('Expected class "%s" should be present', $cls));
        }

        self::assertSame(1, $button->filter('.ids-btn__icon')->count(), 'Icon container should be rendered');
        self::assertSame(0, $button->filter('.ids-btn__label')->count(), 'Label container should not be rendered');

        $use = $button->filter('.ids-btn__icon svg use')->first();
        self::assertSame(1, $use->count(), 'Icon <use> element should exist');
        $href = (string) $use->attr('xlink:href');
        self::assertNotSame('', $href, 'Icon href should not be empty');
        self::assertStringEndsWith('#arrow-right', $href, 'Icon href should end with "#arrow-right"');
    }

    public function testDisabledState(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:IconButton', [
            'icon' => 'arrow-right',
            'disabled' => true,
        ]);
        $button = $rendered->crawler()->filter('button.ids-btn');

        $classAttr = (string) $button->attr('class');
        self::assertStringContainsString('ids-btn--disabled', $classAttr, 'Disabled modifier class should be present');
        self::assertNotNull($button->attr('disabled'), 'Disabled attribute should be present');
    }

    public function testMergesCustomClassesAndAttributes(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:IconButton', [
            'icon' => 'arrow-right',
            'class' => 'u-ml-2 custom-hook',
            'attributes' => [
                'data-test' => 'icon-button-x',
            ],
        ]);

        $button = $rendered->crawler()->filter('button.ids-btn');
        $classStr = (string) $button->attr('class');

        self::assertStringContainsString('ids-btn', $classStr, 'Base button class should be present');
        self::assertStringContainsString('ids-btn--icon-only', $classStr, 'Icon-only modifier should be present');
        self::assertStringContainsString('u-ml-2', $classStr, 'Custom class "u-ml-2" should be merged');
        self::assertStringContainsString('custom-hook', $classStr, 'Custom class "custom-hook" should be merged');

        self::assertSame('icon-button-x', $button->attr('data-test'), 'Custom attribute should be preserved');
    }

    /**
     * @return Generator<string, array{0: array<string,mixed>, 1: list<string>}>
     */
    public static function typeAndSizeProvider(): Generator
    {
        yield 'defaults -> primary + medium' => [
            [],
            ['ids-btn--primary', 'ids-btn--medium'],
        ];

        yield 'secondary + small' => [
            ['type' => 'secondary', 'size' => 'small'],
            ['ids-btn--secondary', 'ids-btn--small'],
        ];

        yield 'secondary-alt + medium' => [
            ['type' => 'secondary-alt'],
            ['ids-btn--secondary-alt', 'ids-btn--medium'],
        ];
    }

    private function getButton(Crawler $crawler): Crawler
    {
        return $crawler->filter('button.ids-btn');
    }
}
