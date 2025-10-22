<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\Checkbox;

use Ibexa\DesignSystemTwig\Twig\Components\Checkbox\Field;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class FieldTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Field::class, $this->baseProps([
            'checked' => true,
            'disabled' => true,
            'required' => true,
            'indeterminate' => true,
        ]));

        self::assertInstanceOf(Field::class, $component, 'Component should mount as Checkbox\\Field.');
    }

    public function testDefaultRenderProducesWrapperLabelAndInput(): void
    {
        $crawler = $this->renderTwigComponent(
            Field::class,
            $this->baseProps(),
            'Accept terms'
        )->crawler();

        $wrapper = $this->getWrapper($crawler);
        $classes = $this->getClassAttr($wrapper);
        self::assertStringContainsString('ids-choice-input-field', $classes, 'Wrapper should include base "ids-choice-input-field".');
        self::assertStringContainsString('ids-checkbox-field', $classes, 'Wrapper should include variant "ids-checkbox-field".');

        $label = $this->getLabel($crawler);
        self::assertSame('agree', $label->attr('for'), 'Label "for" should equal the top-level id.');
        self::assertStringContainsString('Accept terms', $this->getText($label), 'Label should render slot content.');

        $input = $this->getCheckbox($crawler);
        self::assertSame('checkbox', $input->attr('type'), 'Input "type" should be "checkbox".');
        self::assertSame('agree', $input->attr('id'), 'Input "id" should equal provided id.');
        self::assertSame('consent', $input->attr('name'), 'Input "name" should equal provided name.');
    }

    public function testWrapperAttributesMergeClassAndData(): void
    {
        $crawler = $this->renderTwigComponent(
            Field::class,
            $this->baseProps([
                'attributes' => ['class' => 'extra-class', 'data-custom' => 'custom'],
            ]),
            'Label'
        )->crawler();

        $wrapper = $this->getWrapper($crawler);
        $classes = $this->getClassAttr($wrapper);
        self::assertStringContainsString('extra-class', $classes, 'Custom class should be merged on the wrapper.');
        self::assertSame('custom', $wrapper->attr('data-custom'), 'Custom data attribute should render on the wrapper.');
    }

    public function testBooleanFlagsRenderNativeAttributesAndIndeterminateClass(): void
    {
        $crawler = $this->renderTwigComponent(
            Field::class,
            $this->baseProps([
                'checked' => true,
                'disabled' => true,
                'required' => true,
                'indeterminate' => true,
            ]),
            'Label'
        )->crawler();

        $input = $this->getCheckbox($crawler);

        self::assertNotNull($input->attr('disabled'), 'disabled=true should render native "disabled" attribute.');
        self::assertNotNull($input->attr('required'), 'required=true should render native "required" attribute.');
        self::assertNotNull($input->attr('checked'), 'checked=true should render native "checked" attribute.');

        self::assertStringContainsString('ids-input--indeterminate', $this->getClassAttr($input), 'indeterminate=true should add "ids-input--indeterminate" class.');
    }

    public function testInvalidIndeterminateTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Field::class, $this->baseProps([
            'indeterminate' => 'not-bool',
        ]));
    }

    public function testEmptyNameCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Field::class, $this->baseProps([
            'name' => '',
        ]));
    }

    public function testEmptyIdCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(Field::class, $this->baseProps([
            'id' => '',
        ]));
    }

    public function testMissingRequiredOptionsCauseResolverErrorOnMount(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->mountTwigComponent(Field::class, [
            'value' => 'yes',
        ]);
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function baseProps(array $overrides = []): array
    {
        return array_replace([
            'id' => 'agree',
            'name' => 'consent',
            'value' => 'yes',
        ], $overrides);
    }

    private function getWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-choice-input-field')->first();
        self::assertGreaterThan(0, $node->count(), 'Wrapper ".ids-choice-input-field" should be present.');

        return $node;
    }

    private function getLabel(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-choice-input-field .ids-choice-input-label')->first();
        self::assertGreaterThan(0, $node->count(), 'Choice input label should be present.');

        return $node;
    }

    private function getCheckbox(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('input[type="checkbox"]')->first();
        self::assertGreaterThan(0, $node->count(), 'Checkbox input should be present.');

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
