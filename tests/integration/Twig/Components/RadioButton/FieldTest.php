<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\RadioButton;

use Ibexa\DesignSystemTwig\Twig\Components\RadioButton\Field;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class FieldTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            Field::class,
            $this->baseProps()
        );

        self::assertInstanceOf(Field::class, $component, 'Component should mount as radio_button\\Field.');
    }

    public function testDefaultRenderProducesWrapperAndRadioInput(): void
    {
        $crawler = $this->renderTwigComponent(
            Field::class,
            $this->baseProps(),
            'My label'
        )->crawler();

        $wrapper = $this->getWrapper($crawler);
        $class = $this->getClassAttr($wrapper);
        self::assertStringContainsString('ids-radio-button-field', $class, 'Wrapper should have "ids-radio-button-field" class.');
        self::assertStringContainsString('My label', $this->getText($wrapper), 'Wrapper should render provided label content.');

        $input = $this->getRadioInput($crawler);
        self::assertSame('radio', $input->attr('type'), 'Rendered input should be type="radio".');
        self::assertSame('group', $input->attr('name'), 'Input "name" should be passed through.');
    }

    public function testAttributesMergeClassAndDataOnWrapper(): void
    {
        $crawler = $this->renderTwigComponent(
            Field::class,
            $this->baseProps([
                'attributes' => ['class' => 'extra-class', 'data-custom' => 'custom'],
            ]),
            'My label'
        )->crawler();

        $wrapper = $this->getWrapper($crawler);
        $class = $this->getClassAttr($wrapper);

        self::assertStringContainsString('extra-class', $class, 'Custom class should be merged into wrapper.');
        self::assertSame('custom', $wrapper->attr('data-custom'), 'Custom data attribute should be rendered on the wrapper.');
    }

    public function testInputBooleanAttributesAndClassesPassThrough(): void
    {
        $crawler = $this->renderTwigComponent(
            Field::class,
            $this->baseProps([
                'checked' => true,
                'disabled' => true,
                'required' => true,
            ]),
            'My label'
        )->crawler();

        $input = $this->getRadioInput($crawler);

        self::assertNotNull($input->attr('disabled'), 'Disabled should render native "disabled" attribute.');
        self::assertNotNull($input->attr('required'), 'Required should render native "required" attribute.');
    }

    public function testInvalidAttributesTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(
            Field::class,
            [
                'attributes' => ['class' => 'bar'],
                'content' => 'foo',
                'id' => 123,
                'name' => 'group',
                'value' => 'baz',
            ]
        );
    }

    private function getWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-radio-button-field')->first();
        self::assertGreaterThan(0, $node->count(), 'Wrapper with ".ids-radio-button-field" should be present.');

        return $node;
    }

    private function getRadioInput(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-radio-button > input')->first();
        self::assertGreaterThan(0, $node->count(), 'Radio input should be present under ".ids-radio-button > input".');

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

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function baseProps(array $overrides = []): array
    {
        return array_replace([
            'id' => 'id',
            'name' => 'group',
            'value' => 'foo',
            'content' => 'bar',
            'label' => 'Baz',
        ], $overrides);
    }
}
