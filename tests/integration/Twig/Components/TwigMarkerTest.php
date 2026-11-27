<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Twig\Environment;

final class TwigMarkerTest extends KernelTestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideComponentsInitializedByScript(): iterable
    {
        $items = "[{ id: 'a', label: 'A', value: 'a' }, { id: 'b', label: 'B', value: 'b' }]";
        $dropdownItems = "[{ id: 'a', label: 'A' }, { id: 'b', label: 'B' }]";

        yield 'accordion' => ['<twig:ibexa:accordion />', '.ids-accordion'];
        yield 'alert' => ['<twig:ibexa:alert type="info" title="Title" />', '.ids-alert'];
        yield 'alt radio' => ['<twig:ibexa:alt_radio:input name="alt" value="a" label="A" />', '.ids-alt-radio'];
        yield 'alt radio list field' => [
            '<twig:ibexa:alt_radio:list_field name="alt" :items="' . $items . '" />',
            '.ids-alt-radio-list-field',
        ];
        yield 'checkbox' => ['<twig:ibexa:checkbox:input name="check" />', '.ids-input--checkbox'];
        yield 'checkboxes list field' => [
            '<twig:ibexa:checkbox:list_field name="checks" :items="' . $items . '" />',
            '.ids-checkboxes-list-field',
        ];
        yield 'dropdown single' => [
            '<twig:ibexa:dropdown_single:input name="single" :items="' . $dropdownItems . '" />',
            '.ids-dropdown--single',
        ];
        yield 'dropdown multi' => [
            '<twig:ibexa:dropdown_multi:input name="multi" :items="' . $dropdownItems . '" />',
            '.ids-dropdown--multi',
        ];
        yield 'filter dropdown' => [
            '<twig:ibexa:filter_dropdown name="filter" label="Filter" :items="' . $dropdownItems . '" />',
            '.ids-dropdown--filter',
        ];
        yield 'input text' => ['<twig:ibexa:input_text:input name="text" />', '.ids-input-text'];
        yield 'input text field' => ['<twig:ibexa:input_text:field id="text" name="text" />', '.ids-field--input-text'];
        yield 'radio button' => ['<twig:ibexa:radio_button:input name="radio" value="a" />', '.ids-input--radio'];
        yield 'radio buttons list field' => [
            '<twig:ibexa:radio_button:list_field name="radios" :items="' . $items . '" />',
            '.ids-radio-buttons-list-field',
        ];
        yield 'overflow list' => [
            "{% embed '@IbexaDesignSystemTwig/themes/standard/design_system/components/overflow_list.html.twig' with { items: [], item_template_props: {}, hidden_count_template: '' } only %}"
            . '{% block item %}{% endblock %}{% block more_item %}{% endblock %}{% endembed %}',
            '.ids-overflow-list',
        ];
        yield 'tabs' => ['<twig:ibexa:tabs />', '.ids-tabs'];
        yield 'toggle button' => ['<twig:ibexa:toggle_button:input name="toggle" />', '.ids-toggle'];
        yield 'toggle button field' => ['<twig:ibexa:toggle_button:field name="toggle" />', '.ids-toggle-field'];
    }

    #[DataProvider('provideComponentsInitializedByScript')]
    public function testRootCarriesTwigMarker(
        string $template,
        string $rootSelector
    ): void {
        $root = $this->renderTemplate($template)->filter($rootSelector)->first();

        self::assertSame(1, $root->count(), sprintf('Root "%s" should be rendered.', $rootSelector));
        self::assertNotNull($root->attr('data-ids-twig'), sprintf('Root "%s" should carry data-ids-twig.', $rootSelector));
    }

    private function renderTemplate(string $template): Crawler
    {
        $twig = self::getContainer()->get('twig');
        self::assertInstanceOf(Environment::class, $twig);

        return new Crawler($twig->createTemplate($template)->render());
    }
}
