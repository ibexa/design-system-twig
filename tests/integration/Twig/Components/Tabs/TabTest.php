<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\Tabs;

use Generator;
use Ibexa\DesignSystemTwig\Twig\Components\Tabs\Tab;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class TabTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Tab::class, [
            'label' => 'Fields',
            'isSelected' => true,
            'hasError' => true,
            'isDisabled' => false,
            'href' => '#fields',
            'panelId' => 'fields-panel',
            'itemClass' => 'extra-item',
        ]);

        self::assertInstanceOf(Tab::class, $component, 'Component should mount as Tab.');
        self::assertSame('a', $component->getTag(), 'A tab with href should render as an anchor.');
    }

    public function testDefaultRenderProducesButtonTab(): void
    {
        $crawler = $this->renderTwigComponent(Tab::class, ['label' => 'Fields'])->crawler();

        $item = $crawler->filter('li.ids-tabs__item')->first();
        self::assertSame(1, $item->count(), 'Item ".ids-tabs__item" should be rendered.');
        self::assertSame('presentation', $item->attr('role'), 'Item should carry role="presentation".');

        $tab = $item->filter('button.ids-tabs__tab')->first();
        self::assertSame(1, $tab->count(), 'Tab should render as a button by default.');
        self::assertSame('button', $tab->attr('type'), 'Button tab should have type="button".');
        self::assertSame('tab', $tab->attr('role'), 'Tab should carry role="tab".');
        self::assertSame('false', $tab->attr('aria-selected'), 'Unselected tab should have aria-selected="false".');
        self::assertSame('-1', $tab->attr('tabindex'), 'Unselected tab should be out of the tab order.');
        self::assertNull($tab->attr('aria-controls'), 'No aria-controls without panelId.');
        self::assertStringNotContainsString('ids-tabs__tab--selected', (string) $tab->attr('class'));

        $label = $tab->filter('.ids-tabs__tab-label')->first();
        self::assertSame(1, $label->count(), 'Label wrapper should be rendered.');
        self::assertSame('Fields', trim($label->text('')), 'Label should be rendered inside the wrapper.');
        self::assertSame('Fields', $label->attr('data-label'), 'Label wrapper should expose the plain label for width reservation.');
        self::assertSame(0, $tab->filter('.ids-tabs__tab-error-icon')->count(), 'No error icon without hasError.');
    }

    public function testSelectedTabRendersStateAttributes(): void
    {
        $crawler = $this->renderTwigComponent(Tab::class, [
            'label' => 'Fields',
            'isSelected' => true,
            'panelId' => 'fields-panel',
        ])->crawler();

        $tab = $this->getTab($crawler);
        self::assertStringContainsString('ids-tabs__tab--selected', (string) $tab->attr('class'), 'Selected tab should carry the selected modifier.');
        self::assertSame('true', $tab->attr('aria-selected'), 'Selected tab should have aria-selected="true".');
        self::assertSame('0', $tab->attr('tabindex'), 'Selected tab should be in the tab order.');
        self::assertSame('fields-panel', $tab->attr('aria-controls'), 'panelId should map to aria-controls.');
    }

    public function testHrefRendersAnchorTab(): void
    {
        $crawler = $this->renderTwigComponent(Tab::class, [
            'label' => 'Fields',
            'href' => '#ibexa-tab-fields',
            'isDisabled' => true,
            'attributes' => ['data-bs-toggle' => 'tab', 'class' => 'extra-tab'],
        ])->crawler();

        $tab = $crawler->filter('li.ids-tabs__item > a.ids-tabs__tab')->first();
        self::assertSame(1, $tab->count(), 'Tab should render as an anchor when href is given.');
        self::assertSame('#ibexa-tab-fields', $tab->attr('href'), 'Anchor should carry the href.');
        self::assertNull($tab->attr('type'), 'Anchor tab should not carry a type attribute.');
        self::assertSame('true', $tab->attr('aria-disabled'), 'Disabled anchor tab should use aria-disabled.');
        self::assertSame('tab', $tab->attr('data-bs-toggle'), 'Extra attributes should land on the trigger.');
        self::assertStringContainsString('extra-tab', (string) $tab->attr('class'), 'Extra class should merge into the trigger classes.');
        self::assertStringContainsString('ids-tabs__tab--disabled', (string) $tab->attr('class'), 'Disabled tab should carry the disabled modifier.');
    }

    public function testDisabledButtonTabIsDisabled(): void
    {
        $crawler = $this->renderTwigComponent(Tab::class, ['label' => 'Fields', 'isDisabled' => true])->crawler();

        $tab = $this->getTab($crawler);
        self::assertNotNull($tab->attr('disabled'), 'Disabled button tab should carry the disabled attribute.');
    }

    public function testErrorTabRendersIcon(): void
    {
        $crawler = $this->renderTwigComponent(Tab::class, ['label' => 'Fields', 'hasError' => true])->crawler();

        $tab = $this->getTab($crawler);
        self::assertStringContainsString('ids-tabs__tab--error', (string) $tab->attr('class'), 'Error tab should carry the error modifier.');
        self::assertSame(1, $tab->filter('.ids-tabs__tab-error-icon')->count(), 'Error tab should render the error icon.');
    }

    public function testItemClassAndLabelBlock(): void
    {
        $crawler = $this->renderTwigComponent(Tab::class, ['itemClass' => 'extra-item'], '<em class="custom-label">Custom</em>')->crawler();

        $item = $crawler->filter('li.ids-tabs__item')->first();
        self::assertStringContainsString('extra-item', (string) $item->attr('class'), 'itemClass should land on the list item.');
        self::assertSame(1, $crawler->filter('.ids-tabs__tab-label .custom-label')->count(), 'Content block should replace the label inside the wrapper.');
    }

    /**
     * @param array<string, mixed> $props
     */
    #[DataProvider('invalidPropsProvider')]
    public function testInvalidPropsCauseResolverErrorOnMount(array $props): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Tab::class, $props);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>}>
     */
    public static function invalidPropsProvider(): Generator
    {
        yield 'isSelected: string' => [['isSelected' => 'yes']];

        yield 'hasError: int' => [['hasError' => 1]];

        yield 'href: array' => [['href' => ['#x']]];

        yield 'label: int' => [['label' => 5]];
    }

    private function getTab(Crawler $crawler): Crawler
    {
        $tab = $crawler->filter('li.ids-tabs__item > .ids-tabs__tab')->first();
        self::assertSame(1, $tab->count(), 'Tab trigger ".ids-tabs__tab" should be present.');

        return $tab;
    }
}
