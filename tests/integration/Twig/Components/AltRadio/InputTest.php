<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\AltRadio;

use Ibexa\DesignSystemTwig\Twig\Components\AltRadio\Input;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class InputTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Input::class, [
            'id' => 'opt-a',
            'name' => 'group',
            'value' => 'A',
            'required' => true,
            'disabled' => true,
            'checked' => true,
            'error' => false,
            'label' => 'Pick A',
        ]);

        self::assertInstanceOf(Input::class, $component, 'Component should mount as AltRadio\\Input.');
        self::assertSame('group', $component->name, 'Prop "name" should be set.');
        self::assertTrue($component->required, 'Prop "required" should be true.');
        self::assertTrue($component->disabled, 'Prop "disabled" should be true.');
        self::assertSame('radio', $component->getType(), 'getType() should return "radio".');
    }

    public function testDefaultRenderProducesStructureAndInput(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'id' => 'opt-a',
            'name' => 'group',
            'value' => 'A',
            'label' => 'Pick A',
            'attributes' => ['id' => 'opt-a', 'name' => 'group', 'value' => 'A'],
        ])->crawler();

        $wrapper = $this->getWrapper($crawler);
        self::assertStringContainsString('ids-alt-radio', $this->getClassAttr($wrapper), 'Wrapper should have "ids-alt-radio" class.');

        $input = $this->getInput($crawler);
        self::assertSame('radio', $input->attr('type'), 'Input type should be "radio".');
        self::assertSame('opt-a', $input->attr('id'), 'Input "id" should equal provided id.');
        self::assertSame('group', $input->attr('name'), 'Input "name" should equal provided name.');
        self::assertSame('A', $input->attr('value'), 'Input "value" should equal provided value.');

        $tile = $this->getTile($crawler);
        self::assertStringContainsString('ids-alt-radio__tile', $this->getClassAttr($tile), 'Tile should have base tile class.');
        self::assertSame('Pick A', $this->getText($tile), 'Tile should render the provided label.');
        self::assertSame('button', $tile->attr('role'), 'Tile role should be "button".');
    }

    public function testWrapperClassMergeAndInputAttributes(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'id' => 'opt-a',
            'name' => 'group',
            'value' => 'A',
            'label' => 'Pick A',
            'attributes' => [
                'id' => 'opt-a',
                'name' => 'group',
                'value' => 'A',
                'class' => 'extra-class',
                'data-custom' => 'custom',
            ],
        ])->crawler();

        $wrapper = $this->getWrapper($crawler);
        self::assertStringContainsString('extra-class', $this->getClassAttr($wrapper), 'Custom wrapper class should be merged.');

        $input = $this->getInput($crawler);
        self::assertSame('custom', $input->attr('data-custom'), 'Custom data attribute should be rendered on the input element.');
    }

    public function testModifiersAffectTileAndInputAttributes(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'id' => 'opt-a',
            'name' => 'group',
            'value' => 'A',
            'label' => 'Pick A',
            'checked' => true,
            'disabled' => true,
            'required' => true,
            'error' => true,
            'attributes' => ['id' => 'opt-a', 'name' => 'group', 'value' => 'A'],
        ])->crawler();

        $tile = $this->getTile($crawler);
        $tileClass = $this->getClassAttr($tile);
        self::assertStringContainsString('ids-alt-radio__tile--checked', $tileClass, 'Checked should add tile checked modifier.');
        self::assertStringContainsString('ids-alt-radio__tile--disabled', $tileClass, 'Disabled should add tile disabled modifier.');
        self::assertStringContainsString('ids-alt-radio__tile--error', $tileClass, 'Error should add tile error modifier.');

        $input = $this->getInput($crawler);
        self::assertNotNull($input->attr('checked'), 'Checked should render native "checked" attribute.');
        self::assertNotNull($input->attr('disabled'), 'Disabled should render native "disabled" attribute.');
        self::assertNotNull($input->attr('required'), 'Required should render native "required" attribute.');
    }

    public function testInvalidLabelTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Input::class, [
            'id' => 'opt-a',
            'name' => 'group',
            'value' => 'A',
            'label' => ['not-a-string'],
        ]);
    }

    public function testMissingRequiredOptionsCauseResolverErrorOnMount(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->mountTwigComponent(Input::class, [
            'value' => 'A',
            'label' => 'Pick A',
        ]);
    }

    private function getWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-alt-radio')->first();
        self::assertGreaterThan(0, $node->count(), 'Wrapper ".ids-alt-radio" should be present.');

        return $node;
    }

    private function getInput(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-alt-radio__source > input')->first();
        self::assertGreaterThan(0, $node->count(), 'Input should be present under ".ids-alt-radio__source > input".');

        return $node;
    }

    private function getTile(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-alt-radio__tile')->first();
        self::assertGreaterThan(0, $node->count(), 'Tile ".ids-alt-radio__tile" should be present.');

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
