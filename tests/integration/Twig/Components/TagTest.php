<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Generator;
use Ibexa\DesignSystemTwig\Twig\Components\Tag;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class TagTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Tag::class, [
            'type' => 'primary',
            'size' => 'small',
            'isDark' => true,
            'is_ghost_type' => true,
            'icon' => 'check',
        ]);

        self::assertInstanceOf(Tag::class, $component, 'Component should mount as Tag.');
    }

    public function testDefaultRenderProducesWrapperAndRendersSlot(): void
    {
        $crawler = $this->renderTwigComponent(
            Tag::class,
            [],
            'Hello tag'
        )->crawler();

        $wrapper = $this->getWrapper($crawler);

        self::assertStringContainsString('ids-tag', $this->getClassAttr($wrapper), 'Wrapper should include base class "ids-tag".');
        self::assertStringContainsString('Hello tag', trim($wrapper->text('')), 'Slot content should be rendered inside the tag.');
    }

    /**
     * @param array<string, mixed> $props
     * @param list<string> $expectedClasses
     */
    #[DataProvider('variantProvider')]
    public function testVariantsProduceExpectedClasses(array $props, array $expectedClasses): void
    {
        $crawler = $this->renderTwigComponent(
            Tag::class,
            $props,
            'X'
        )->crawler();

        $wrapper = $this->getWrapper($crawler);
        $class = $this->getClassAttr($wrapper);

        foreach ($expectedClasses as $cls) {
            self::assertStringContainsString($cls, $class, sprintf('Expected class "%s" should be present.', $cls));
        }
    }

    public static function variantProvider(): Generator
    {
        yield 'size: small' => [['size' => 'small'],  ['ids-tag--small']];

        yield 'size: medium' => [['size' => 'medium'], ['ids-tag--medium']];

        yield 'isDark: true' => [['isDark' => true], ['ids-tag--dark']];

        $types = [
            'primary',
            'success',
            'info',
            'warning',
            'error',
            'neutral',
            'icon-tag',
            'primary-alt',
            'success-ghost',
            'error-ghost',
            'neutral-ghost',
        ];

        foreach ($types as $type) {
            yield "type: {$type}" => [['type' => $type], ["ids-tag--{$type}"]];
        }
    }

    public function testGhostTypeRendersDot(): void
    {
        $crawler = $this->renderTwigComponent(
            Tag::class,
            ['type' => 'neutral-ghost'],
            'Ghost'
        )->crawler();

        $dot = $crawler->filter('.ids-tag__ghost-dot, .ids-tag__dot')->first();

        self::assertGreaterThan(0, $dot->count(), 'Ghost dot should be rendered for ghost tag types.');
    }

    public function testIconRendersIconContainer(): void
    {
        $crawler = $this->renderTwigComponent(
            Tag::class,
            ['icon' => 'check'],
            'With icon'
        )->crawler();

        $icon = $crawler->filter('.ids-tag__icon')->first();

        self::assertGreaterThan(0, $icon->count(), 'Icon container should be rendered when "icon" is provided.');
    }

    public function testAttributesMergeClass(): void
    {
        $crawler = $this->renderTwigComponent(
            Tag::class,
            ['attributes' => ['class' => 'extra-class']],
            'Merge'
        )->crawler();

        $wrapper = $this->getWrapper($crawler);

        self::assertStringContainsString(
            'extra-class',
            $this->getClassAttr($wrapper),
            'Custom class should be merged into wrapper class attribute.'
        );
    }

    public function testInvalidSizeValueCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Tag::class, ['size' => 'giant']);
    }

    public function testInvalidTypeValueCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Tag::class, ['type' => 'unknown']);
    }

    public function testInvalidIsDarkTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Tag::class, ['isDark' => 'yes']);
    }

    private function getWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('div.ids-tag')->first();
        self::assertGreaterThan(0, $node->count(), 'Tag wrapper ".ids-tag" should be present.');

        return $node;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }
}
