<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\Tabs;

use Ibexa\DesignSystemTwig\Twig\Components\Tabs\Panel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class PanelTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Panel::class, ['id' => 'panel-fields', 'labelledBy' => 'tab-fields', 'isSelected' => true]);

        self::assertInstanceOf(Panel::class, $component, 'Component should mount as Panel.');
        self::assertSame('panel-fields', $component->id);
    }

    public function testHiddenPanelRender(): void
    {
        $crawler = $this->renderTwigComponent(Panel::class, ['id' => 'panel-fields', 'labelledBy' => 'tab-fields'], 'Panel body')->crawler();

        $panel = $crawler->filter('div.ids-tabs__panel')->first();
        self::assertSame(1, $panel->count(), 'Panel ".ids-tabs__panel" should be rendered.');
        self::assertSame('panel-fields', $panel->attr('id'), 'Panel should carry its id.');
        self::assertSame('tabpanel', $panel->attr('role'), 'Panel should carry role="tabpanel".');
        self::assertSame('tab-fields', $panel->attr('aria-labelledby'), 'labelledBy should map to aria-labelledby.');
        self::assertNotNull($panel->attr('hidden'), 'Unselected panel should be hidden.');
        self::assertStringNotContainsString('ids-tabs__panel--selected', (string) $panel->attr('class'));
        self::assertSame('Panel body', trim($panel->text('')), 'Content block should render inside the panel.');
    }

    public function testSelectedPanelRender(): void
    {
        $crawler = $this->renderTwigComponent(Panel::class, ['id' => 'panel-fields', 'isSelected' => true], 'Panel body')->crawler();

        $panel = $crawler->filter('div.ids-tabs__panel')->first();
        self::assertStringContainsString('ids-tabs__panel--selected', (string) $panel->attr('class'), 'Selected panel should carry the selected modifier.');
        self::assertNull($panel->attr('hidden'), 'Selected panel should not be hidden.');
        self::assertNull($panel->attr('aria-labelledby'), 'No aria-labelledby without labelledBy.');
    }

    public function testMissingIdCausesResolverErrorOnMount(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->mountTwigComponent(Panel::class, []);
    }

    public function testEmptyIdCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Panel::class, ['id' => ' ']);
    }
}
