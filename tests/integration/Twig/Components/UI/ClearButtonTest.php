<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\UI;

use Ibexa\DesignSystemTwig\Twig\Components\UI\ClearButton;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ClearButtonTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(ClearButton::class, ['disabled' => true]);

        self::assertInstanceOf(ClearButton::class, $component, 'Component should mount as UI\\ClearButton.');
        self::assertTrue($component->disabled, 'Prop "disabled" should be true.');
    }

    public function testDefaultRenderProducesConfiguredButton(): void
    {
        $crawler = $this->renderTwigComponent(ClearButton::class)->crawler();

        $button = $this->getButton($crawler);
        $class = $this->getClassAttr($button);

        self::assertStringContainsString('ids-btn', $class, 'Base button class "ids-btn" should be present.');
        self::assertStringContainsString('ids-btn--tertiary-alt', $class, 'Type "tertiary-alt" should add the corresponding modifier class.');
        self::assertStringContainsString('ids-btn--small', $class, 'Size "small" should add the corresponding modifier class.');
        self::assertStringContainsString('ids-clear-btn', $class, 'Custom class "ids-clear-btn" should be merged onto the button.');

        $title = (string) $button->attr('title');
        $aria = (string) $button->attr('aria-label');
        self::assertNotSame('', $title, 'Title should not be empty.');
        self::assertSame($title, $aria, 'Title and aria-label should be identical.');

        $iconUse = $this->getIconUse($crawler);
        $href = (string) $iconUse->attr('xlink:href');
        self::assertStringContainsString('#discard', $href, 'Icon href should reference the "discard" symbol.');

        $label = $crawler->filter('.ids-btn__label');
        self::assertSame(0, $label->count(), 'Icon-only button should not render a label container.');
    }

    public function testDisabledTrueAddsAttributeAndClass(): void
    {
        $crawler = $this->renderTwigComponent(ClearButton::class, ['disabled' => true])->crawler();

        $button = $this->getButton($crawler);
        $class = $this->getClassAttr($button);

        self::assertNotNull($button->attr('disabled'), 'Disabled prop should render native "disabled" attribute on <button>.');
        self::assertStringContainsString('ids-btn--disabled', $class, 'Disabled prop should add "ids-btn--disabled" class.');
    }

    public function testInvalidDisabledTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(ClearButton::class, ['disabled' => 'yes']);
    }

    private function getButton(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('button.ids-btn')->first();
        self::assertGreaterThan(0, $node->count(), 'Button element "button.ids-btn" should be present.');

        return $node;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }

    private function getIconUse(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('button.ids-btn svg use')->first();
        self::assertGreaterThan(0, $node->count(), '<use> element inside the button icon should be present.');

        return $node;
    }
}
