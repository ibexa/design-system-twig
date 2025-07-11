<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\CustomIcon;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class CustomIconTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            'ibexa:custom-icon',
            [
                'path' => '/assets/example.svg',
            ]
        );

        self::assertInstanceOf(CustomIcon::class, $component);
        self::assertSame('/assets/example.svg', $component->path);
    }

    public function testDefaultRender(): void
    {
        $rendered = $this->renderTwigComponent(
            'ibexa:custom-icon',
            [
                'path' => '/assets/example.svg',
            ]
        );

        $crawler = $rendered->crawler();

        self::assertStringContainsString(
            'ids-icon',
            $crawler->filter('svg')->attr('class'),
        );

        self::assertStringEndsWith(
            '/assets/example.svg',
            $crawler->filter('svg use')->first()->attr('xlink:href')
        );
    }
}
