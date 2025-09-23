<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\Icon;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class IconTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            'ibexa:icon',
            [
                'name' => 'trash',
            ]
        );

        self::assertInstanceOf(Icon::class, $component);
        self::assertSame('trash', $component->name);
    }

    public function testDefaultRender(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:icon',
            [
                'name' => 'trash',
            ]
        );

        $actual = $rendered->crawler()->filter('svg use')->first()->attr('xlink:href');

        self::assertNotNull($actual);
        self::assertStringEndsWith(
            '#trash',
            $actual
        );
    }

    public function testCustomPathRender(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:icon',
            [
                'path' => '/assets/example.svg',
            ]
        );

        $crawler = $rendered->crawler();

        $class = $crawler->filter('svg')->attr('class');

        self::assertNotNull($class);
        self::assertStringContainsString(
            'ids-icon',
            $class,
        );

        $href = $crawler->filter('svg use')->first()->attr('xlink:href');

        self::assertNotNull($href);
        self::assertStringEndsWith(
            '/assets/example.svg',
            $href
        );
    }

    public function testCustomPathWithNameRender(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:icon',
            [
                'name' => 'trash',
                'path' => '/assets/example.svg',
            ]
        );

        $crawler = $rendered->crawler();

        $class = $crawler->filter('svg')->attr('class');

        self::assertNotNull($class);
        self::assertStringContainsString(
            'ids-icon',
            $class,
        );

        $href = $crawler->filter('svg use')->first()->attr('xlink:href');

        self::assertNotNull($href);
        self::assertStringEndsWith(
            '/assets/example.svg',
            $href
        );
    }
}
