<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\Badge;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class BadgeTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Badge::class, [
            'size' => 'small',
            'value' => '7',
            'maxValue' => 15,
            'variant' => 'number',
        ]);

        self::assertInstanceOf(Badge::class, $component, 'Component should mount as Badge.');
        self::assertSame('small', $component->size, 'Prop "size" should be "small".');
        self::assertSame('7', $component->value, 'Prop "value" should be "7".');
        self::assertSame(15, $component->maxValue, 'Prop "maxValue" should be 15.');
        self::assertSame('number', $component->variant, 'Prop "variant" should be "number".');
    }

    public function testDefaultRender(): void
    {
        $crawler = $this->renderTwigComponent(Badge::class, ['value' => '1'])->crawler();
        $badge = $this->getBadge($crawler);
        $class = $this->getClassAttr($badge);

        self::assertStringContainsString('ids-badge', $class, 'Base class "ids-badge" should be present.');
        self::assertStringNotContainsString('ids-badge--small', $class, 'Default size should be "medium", not "small".');
        self::assertStringNotContainsString('ids-badge--stretched', $class, 'With value below threshold, stretched modifier should not be present.');
        self::assertSame('1', $this->getText($badge), 'Badge text should equal value when under the cap.');
        self::assertSame('99', $badge->attr('data-ids-max-badge-value'), 'Default max should be 99.');
    }

    public function testSizeVariantSmallAddsClass(): void
    {
        $crawler = $this->renderTwigComponent(Badge::class, ['size' => 'small', 'value' => '1'])->crawler();
        $badge = $this->getBadge($crawler);
        $class = $this->getClassAttr($badge);

        self::assertStringContainsString('ids-badge--small', $class, 'Small size should add "ids-badge--small" class.');
    }

    /**
     * @param non-empty-string $size
     */
    #[DataProvider('stretchedProvider')]
    public function testStretchedModifier(string $size, int|string $value, string $variant, bool $expected): void
    {
        $crawler = $this->renderTwigComponent(Badge::class, [
            'size' => $size,
            'value' => $value,
            'variant' => $variant,
        ])->crawler();
        $badge = $this->getBadge($crawler);
        $class = $this->getClassAttr($badge);

        if ($expected) {
            self::assertStringContainsString('ids-badge--stretched', $class, 'Expected stretched modifier to be present.');
        } else {
            self::assertStringNotContainsString('ids-badge--stretched', $class, 'Expected stretched modifier to be absent.');
        }
    }

    /**
     * @return iterable<string, array{0: string, 1: int|string, 2: string, 3: bool}>
     */
    public static function stretchedProvider(): iterable
    {
        yield 'medium below number' => ['medium', '99', 'number', false];
        yield 'medium at number' => ['medium', '100', 'number', true];
        yield 'small below number' => ['small', '9', 'number', false];
        yield 'small at number' => ['small', '10', 'number', true];
        yield 'medium below string' => ['medium', 'ab', 'string', false];
        yield 'medium at string' => ['medium', 'abc', 'string', true];
        yield 'small below string' => ['small', 'a', 'string', false];
        yield 'small at string' => ['small', 'ab', 'string', true];
    }

    public function testFormattedValueIsCappedByMax(): void
    {
        $crawler = $this->renderTwigComponent(Badge::class, [
            'value' => '150',
            'maxValue' => 99,
            'variant' => 'number',
        ])->crawler();

        $badge = $this->getBadge($crawler);
        self::assertSame('99+', $this->getText($badge), 'When value exceeds maxValue, text should display "<max>+".');
        self::assertSame('99', $badge->attr('data-ids-max-badge-value'), 'data-ids-max-badge-value should match the exposed "max_value".');
    }

    public function testFormattedValueDisplaysRawValueWhenUnderMax(): void
    {
        $crawler = $this->renderTwigComponent(Badge::class, [
            'value' => '42',
            'maxValue' => 99,
            'variant' => 'number',
        ])->crawler();

        $badge = $this->getBadge($crawler);
        self::assertSame('42', $this->getText($badge), 'When value <= maxValue, text should display the raw value.');
    }

    public function testFormattedValueIgnoresMaxForStringVariant(): void
    {
        $crawler = $this->renderTwigComponent(Badge::class, [
            'value' => '150',
            'maxValue' => 99,
            'variant' => 'string',
        ])->crawler();

        $badge = $this->getBadge($crawler);
        self::assertSame('150', $this->getText($badge), 'String variant should display the raw value regardless of maxValue.');
    }

    public function testInvalidPropsCauseResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Badge::class, ['size' => 'giant', 'value' => 1]);
    }

    public function testInvalidValueTypeCauseResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Badge::class, ['value' => ['not-scalar']]);
    }

    private function getBadge(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('div.ids-badge')->first();
        self::assertGreaterThan(0, $node->count(), 'Badge wrapper ".ids-badge" should be present.');

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
