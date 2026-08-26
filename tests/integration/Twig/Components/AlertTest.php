<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Generator;
use Ibexa\DesignSystemTwig\Twig\Components\Alert;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;
use Twig\Environment;
use Twig\Markup;

final class AlertTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    private const REQUIRED_PROPS = [
        'type' => 'success',
        'title' => 'Alert title',
    ];

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Alert::class, [
            'type' => 'warning',
            'variant' => 'local',
            'title' => 'Warning',
            'icon' => 'lock',
            'isDismissible' => true,
            'role' => 'status',
        ]);

        self::assertInstanceOf(Alert::class, $component, 'Component should mount as Alert.');
    }

    public function testDefaultRenderProducesClassPlan(): void
    {
        $crawler = $this->renderTwigComponent(Alert::class, self::REQUIRED_PROPS)->crawler();
        $wrapper = $this->getWrapper($crawler);
        $class = $this->getClassAttr($wrapper);

        self::assertStringContainsString('ids-alert--success', $class, 'Type modifier should be present.');
        self::assertStringContainsString('ids-alert--floating', $class, 'Default variant modifier should be "floating".');
        self::assertSame('status', $wrapper->attr('role'), 'Success alerts should default to role="status".');
        self::assertCount(1, $crawler->filter('.ids-alert > .ids-alert__icon'), 'Icon should be a direct child of the root.');
        self::assertCount(1, $crawler->filter('.ids-alert > .ids-alert__content > .ids-alert__title'), 'Title should be rendered inside the content wrapper.');
        self::assertSame('Alert title', trim($crawler->filter('.ids-alert__title')->text('')), 'Title text should be rendered.');
        self::assertCount(0, $crawler->filter('.ids-alert__description'), 'Description should not be rendered without content.');
        self::assertCount(0, $crawler->filter('.ids-alert__actions'), 'Actions should not be rendered without the actions block.');
        self::assertCount(0, $crawler->filter('.ids-alert__close-btn'), 'Close button should not be rendered by default.');
    }

    /**
     * @param array<string, mixed> $props
     * @param list<string> $expectedClasses
     */
    #[DataProvider('variantProvider')]
    public function testVariantsProduceExpectedClasses(array $props, array $expectedClasses): void
    {
        $crawler = $this->renderTwigComponent(Alert::class, $props + self::REQUIRED_PROPS)->crawler();
        $class = $this->getClassAttr($this->getWrapper($crawler));

        foreach ($expectedClasses as $expectedClass) {
            self::assertStringContainsString($expectedClass, $class, sprintf('Expected class "%s" should be present.', $expectedClass));
        }
    }

    public static function variantProvider(): Generator
    {
        foreach (['success', 'warning', 'error', 'info'] as $type) {
            yield "type: {$type}" => [['type' => $type], ["ids-alert--{$type}"]];
        }

        foreach (['floating', 'local', 'toast'] as $variant) {
            yield "variant: {$variant}" => [['variant' => $variant], ["ids-alert--{$variant}"]];
        }
    }

    /**
     * @param array<string, mixed> $props
     * @param non-empty-string $expectedHrefSuffix
     */
    #[DataProvider('iconProvider')]
    public function testIconResolution(array $props, string $expectedHrefSuffix): void
    {
        $crawler = $this->renderTwigComponent(Alert::class, $props + self::REQUIRED_PROPS)->crawler();
        $use = $crawler->filter('.ids-alert__icon use')->first();

        self::assertGreaterThan(0, $use->count(), 'Icon should render a <use> element.');
        self::assertStringEndsWith($expectedHrefSuffix, (string) $use->attr('xlink:href'), 'Icon href should resolve as expected.');
    }

    public static function iconProvider(): Generator
    {
        yield 'success default' => [['type' => 'success'], '#check-circle'];

        yield 'warning default' => [['type' => 'warning'], '#alert-warning'];

        yield 'error default' => [['type' => 'error'], '#alert-error'];

        yield 'info default' => [['type' => 'info'], '#info-rounded'];

        yield 'icon override' => [['icon' => 'lock'], '#lock'];

        yield 'iconPath override wins' => [['icon' => 'lock', 'iconPath' => '/custom/sprite.svg#hide'], '/custom/sprite.svg#hide'];
    }

    /**
     * @param array<string, mixed> $props
     */
    #[DataProvider('roleProvider')]
    public function testRoleResolution(array $props, string $expectedRole): void
    {
        $crawler = $this->renderTwigComponent(Alert::class, $props + self::REQUIRED_PROPS)->crawler();

        self::assertSame($expectedRole, $this->getWrapper($crawler)->attr('role'), 'Role should be derived from type unless overridden.');
    }

    public static function roleProvider(): Generator
    {
        yield 'success -> status' => [['type' => 'success'], 'status'];

        yield 'info -> status' => [['type' => 'info'], 'status'];

        yield 'warning -> alert' => [['type' => 'warning'], 'alert'];

        yield 'error -> alert' => [['type' => 'error'], 'alert'];

        yield 'error with role override' => [['type' => 'error', 'role' => 'status'], 'status'];
    }

    public function testDismissibleRendersCloseButton(): void
    {
        $crawler = $this->renderTwigComponent(Alert::class, ['isDismissible' => true] + self::REQUIRED_PROPS)->crawler();
        $closeBtn = $crawler->filter('.ids-alert > button.ids-alert__close-btn')->first();

        self::assertGreaterThan(0, $closeBtn->count(), 'Close button should be rendered when isDismissible is true.');
        self::assertStringContainsString('ids-btn--tertiary-alt', $this->getClassAttr($closeBtn), 'Close button should be a tertiary-alt button.');
        self::assertStringContainsString('ids-btn--small', $this->getClassAttr($closeBtn), 'Close button should be small.');
        self::assertSame('Close', $closeBtn->attr('aria-label'), 'Close button should carry a translated aria-label.');
    }

    public function testContentBlockRendersDescription(): void
    {
        $crawler = $this->renderTwigComponent(Alert::class, self::REQUIRED_PROPS, 'Alert <strong>description</strong>')->crawler();
        $description = $crawler->filter('.ids-alert__content > .ids-alert__description')->first();

        self::assertGreaterThan(0, $description->count(), 'Description should be rendered inside the content wrapper.');
        self::assertCount(1, $description->filter('strong'), 'Description block should keep its markup.');
    }

    public function testActionsBlockRendersActions(): void
    {
        $twig = self::getContainer()->get('twig');
        self::assertInstanceOf(Environment::class, $twig);

        $html = $twig->createTemplate(
            <<<'TWIG'
            <twig:ibexa:alert type="success" title="Alert title">
                <twig:block name="content">Description</twig:block>
                <twig:block name="actions"><a href="#" class="test-action">Action</a></twig:block>
            </twig:ibexa:alert>
            TWIG
        )->render();
        $crawler = new Crawler($html);
        $actions = $crawler->filter('.ids-alert__content > .ids-alert__actions')->first();

        self::assertGreaterThan(0, $actions->count(), 'Actions should be rendered inside the content wrapper.');
        self::assertCount(1, $actions->filter('.test-action'), 'Actions block content should be rendered.');
        self::assertCount(1, $crawler->filter('.ids-alert__description'), 'Description should still be rendered next to the actions.');
    }

    public function testAttributesMergeClass(): void
    {
        $crawler = $this->renderTwigComponent(
            Alert::class,
            ['attributes' => ['class' => 'extra-class', 'data-test' => 'alert']] + self::REQUIRED_PROPS
        )->crawler();
        $wrapper = $this->getWrapper($crawler);

        self::assertStringContainsString('extra-class', $this->getClassAttr($wrapper), 'Custom class should be merged into the root class attribute.');
        self::assertSame('alert', $wrapper->attr('data-test'), 'Extra attributes should be rendered on the root.');
    }

    public function testMissingTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->mountTwigComponent(Alert::class, ['title' => 'No type']);
    }

    public function testMarkupTitleIsRenderedRaw(): void
    {
        $crawler = $this->renderTwigComponent(
            Alert::class,
            ['type' => 'info', 'title' => new Markup('Exit with <svg class="test-icon"></svg> or Esc', 'UTF-8')]
        )->crawler();
        $title = $crawler->filter('.ids-alert__title')->first();

        self::assertGreaterThan(0, $title->count(), 'Markup titles should render the title element.');
        self::assertCount(1, $title->filter('svg.test-icon'), 'Markup titles should be rendered unescaped, like legacy alert titles.');
    }

    public function testDescriptionOnlyRendersNoTitle(): void
    {
        $crawler = $this->renderTwigComponent(Alert::class, ['type' => 'info'], 'Description only')->crawler();

        self::assertCount(0, $crawler->filter('.ids-alert__title'), 'Title should not be rendered when empty.');
        self::assertCount(1, $crawler->filter('.ids-alert__description'), 'Description should be rendered without a title.');
    }

    public function testInvalidTypeValueCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Alert::class, ['type' => 'danger'] + self::REQUIRED_PROPS);
    }

    public function testInvalidVariantValueCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Alert::class, ['variant' => 'inline'] + self::REQUIRED_PROPS);
    }

    public function testInvalidRoleValueCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Alert::class, ['role' => 'note'] + self::REQUIRED_PROPS);
    }

    public function testInvalidTitleTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Alert::class, ['type' => 'info', 'title' => ['not', 'a', 'string']]);
    }

    public function testInvalidIsDismissibleTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Alert::class, ['isDismissible' => 'yes'] + self::REQUIRED_PROPS);
    }

    private function getWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('div.ids-alert')->first();
        self::assertGreaterThan(0, $node->count(), 'Alert root ".ids-alert" should be present.');

        return $node;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }
}
