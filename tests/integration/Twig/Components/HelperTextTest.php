<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Generator;
use Ibexa\DesignSystemTwig\Twig\Components\HelperText;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class HelperTextTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            'ibexa:HelperText',
            [
                'type' => 'error',
            ]
        );

        self::assertInstanceOf(HelperText::class, $component, 'Component should be instance of HelperText');
        self::assertSame('error', $component->type, 'Type should be "error"');
        self::assertSame('alert-error', $component->iconName(), 'Icon name should be passed through');
    }

    public function testDefaultRender(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:HelperText');
        $crawler = $rendered->crawler();

        $container = $this->getHelperText($crawler);

        self::assertSame(1, $container->count(), 'There should be exactly one helper text container');
        $classAttr = (string) $container->attr('class');

        self::assertStringContainsString('ids-helper-text', $classAttr, 'Base helper-text class should be present');
        self::assertStringContainsString('ids-helper-text--default', $classAttr, 'Default variant should be "default"');

        self::assertSame(1, $container->filter('.ids-helper-text__icon-wrapper')->count(), 'Icon wrapper should be rendered');
        self::assertSame(1, $container->filter('.ids-helper-text__content-wrapper')->count(), 'Content wrapper should be rendered');
    }

    public function testErrorVariantAddsErrorClass(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:HelperText', [
            'type' => 'error',
        ]);
        $crawler = $rendered->crawler();

        $container = $this->getHelperText($crawler);
        $classAttr = (string) $container->attr('class');

        self::assertStringContainsString('ids-helper-text--error', $classAttr, 'Error variant should add error modifier class');
        self::assertStringNotContainsString('ids-helper-text--default', $classAttr, 'Default class should not be present when type=error');
    }

    public function testMergesCustomClassesFromAttributes(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:HelperText', [
            'attributes' => [
                'class' => 'u-mb-2 custom-class',
                'data-test' => 'helper-text-x',
            ],
        ]);
        $crawler = $rendered->crawler();

        $container = $this->getHelperText($crawler);
        $classAttr = (string) $container->attr('class');

        self::assertStringContainsString('ids-helper-text', $classAttr, 'Base class should be present');
        self::assertStringContainsString('u-mb-2', $classAttr, 'Custom class u-mb-2 should be merged');
        self::assertStringContainsString('custom-class', $classAttr, 'Custom class custom-class should be merged');
        self::assertSame('helper-text-x', $container->attr('data-test'), 'Custom attribute should be preserved');
    }

    /**
     * @dataProvider iconByTypeProvider
     *
     * @param array<string,mixed> $props
     * @param non-empty-string $expectedIconId
     */
    public function testIconIsRenderedForType(array $props, string $expectedIconId): void
    {
        $rendered = $this->renderTwigComponent('ibexa:HelperText', $props);

        $iconUse = $rendered->crawler()->filter('.ids-helper-text__icon use')->first();
        self::assertSame(1, $iconUse->count(), 'Icon <use> element should be rendered');

        $href = $iconUse->attr('xlink:href');
        self::assertNotEmpty($href, 'Icon href should not be empty');
        self::assertStringEndsWith(
            $expectedIconId,
            $href,
            sprintf('Icon href should end with "%s" for props: %s', $expectedIconId, json_encode($props))
        );
    }

    /**
     * @return Generator<string, array{0: array<string, mixed>, 1: non-empty-string}>
     */
    public static function iconByTypeProvider(): Generator
    {
        yield 'default type -> info icon' => [
            [],
            '#info-circle',
        ];

        yield 'error type -> alert icon' => [
            ['type' => 'error'],
            '#alert-error',
        ];
    }

    private function getHelperText(Crawler $crawler): Crawler
    {
        return $crawler->filter('div.ids-helper-text');
    }
}
