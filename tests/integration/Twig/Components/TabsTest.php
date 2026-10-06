<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\Tabs;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;
use Twig\Environment;

final class TabsTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Tabs::class, ['listClass' => 'extra-list']);

        self::assertInstanceOf(Tabs::class, $component, 'Component should mount as Tabs.');
        self::assertSame('extra-list', $component->listClass);
    }

    public function testDefaultRenderProducesRootAndList(): void
    {
        $crawler = $this->renderTwigComponent(Tabs::class, [], '')->crawler();

        $root = $crawler->filter('div.ids-tabs')->first();
        self::assertSame(1, $root->count(), 'Root ".ids-tabs" should be rendered.');

        $list = $root->filter('ul.ids-tabs__list')->first();
        self::assertSame(1, $list->count(), 'List ".ids-tabs__list" should be rendered inside the root.');
        self::assertSame('tablist', $list->attr('role'), 'List should carry role="tablist".');
        self::assertSame(0, $root->filter('.ids-tabs__panels')->count(), 'Panels wrapper should not render without panels.');
    }

    public function testRendersTabsPanelsAndExtraClasses(): void
    {
        $crawler = $this->renderTemplate(<<<'TWIG'
            <twig:ibexa:tabs listClass="extra-list" class="extra-root" data-ids-custom-init="true">
                <twig:ibexa:tabs:tab label="First" :isSelected="true" panelId="panel-first" id="tab-first" />
                <twig:ibexa:tabs:tab label="Second" panelId="panel-second" id="tab-second" />
                <twig:block name="after_list"><span class="after-list">After</span></twig:block>
                <twig:block name="panels">
                    <twig:ibexa:tabs:panel id="panel-first" labelledBy="tab-first" :isSelected="true">First panel</twig:ibexa:tabs:panel>
                    <twig:ibexa:tabs:panel id="panel-second" labelledBy="tab-second">Second panel</twig:ibexa:tabs:panel>
                </twig:block>
            </twig:ibexa:tabs>
            TWIG);

        $root = $crawler->filter('div.ids-tabs')->first();
        self::assertStringContainsString('extra-root', (string) $root->attr('class'), 'Root should merge the custom class.');
        self::assertSame('true', $root->attr('data-ids-custom-init'), 'Root should pass through data attributes.');

        $list = $root->filter('ul.ids-tabs__list')->first();
        self::assertStringContainsString('extra-list', (string) $list->attr('class'), 'List should carry the listClass value.');
        self::assertSame(2, $list->filter('li.ids-tabs__item > .ids-tabs__tab')->count(), 'Both tabs should render inside the list.');

        $afterList = $root->filter('ul.ids-tabs__list + .after-list');
        self::assertSame(1, $afterList->count(), 'The after_list block should render right after the list.');

        $panels = $root->filter('.ids-tabs__panels')->first();
        self::assertSame(1, $panels->count(), 'Panels wrapper should render when the panels block has content.');
        self::assertSame(2, $panels->filter('.ids-tabs__panel')->count(), 'Both panels should render inside the wrapper.');
    }

    public function testInvalidListClassTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Tabs::class, ['listClass' => ['not', 'a', 'string']]);
    }

    private function renderTemplate(string $template): Crawler
    {
        $twig = self::getContainer()->get('twig');
        self::assertInstanceOf(Environment::class, $twig);

        return new Crawler($twig->createTemplate($template)->render());
    }
}
