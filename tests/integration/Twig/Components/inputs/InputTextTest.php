<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\inputs;

use Ibexa\DesignSystemTwig\Twig\Components\inputs\InputText;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class InputTextTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            InputText::class,
            [
                'type' => 'email',
                'size' => 'small',
                'disabled' => true,
                'error' => true,
                'required' => true,
            ]
        );

        self::assertInstanceOf(InputText::class, $component, 'Component should mount as inputs\\InputText.');
        self::assertSame('email', $component->type, 'Prop "type" should be "email".');
        self::assertSame('small', $component->size, 'Prop "size" should be "small".');
        self::assertTrue($component->disabled, 'Prop "disabled" should be true.');
        self::assertTrue($component->error, 'Prop "error" should be true.');
        self::assertTrue($component->required, 'Prop "required" should be true.');
    }

    public function testDefaultRender(): void
    {
        $crawler = $this->renderTwigComponent(InputText::class, [])->crawler();
        $input = $this->getInput($crawler);

        $class = (string) $input->attr('class');
        self::assertStringContainsString('ids-input', $class, 'Base class "ids-input" should be present.');
        self::assertStringContainsString('ids-input--text', $class, 'Default type should be "text".');
        self::assertStringContainsString('ids-input--medium', $class, 'Default size should be "medium".');
        self::assertStringNotContainsString('ids-input--disabled', $class, 'Disabled modifier should not be present by default.');
        self::assertStringNotContainsString('ids-input--error', $class, 'Error modifier should not be present by default.');
        self::assertStringNotContainsString('ids-input--required', $class, 'Required modifier should not be present by default.');
    }

    /**
     * @param array<string, mixed> $props
     * @param list<string> $expectedClasses
     */
    #[DataProvider('variantProvider')]
    public function testVariantClasses(array $props, array $expectedClasses): void
    {
        $crawler = $this->renderTwigComponent(InputText::class, $props)->crawler();
        $input = $this->getInput($crawler);
        $class = (string) $input->attr('class');

        foreach ($expectedClasses as $cls) {
            self::assertStringContainsString($cls, $class, sprintf('Expected class "%s" should be present.', $cls));
        }
    }

    public static function variantProvider(): \Generator
    {
        yield 'type: email' => [['type' => 'email'], ['ids-input--email']];
        yield 'type: password' => [['type' => 'password'], ['ids-input--password']];
        yield 'type: number' => [['type' => 'number'], ['ids-input--number']];
        yield 'type: tel' => [['type' => 'tel'], ['ids-input--tel']];
        yield 'type: search' => [['type' => 'search'], ['ids-input--search']];
        yield 'type: url' => [['type' => 'url'], ['ids-input--url']];
        yield 'size: small' => [['size' => 'small'], ['ids-input--small']];
        yield 'size: medium' => [['size' => 'medium'], ['ids-input--medium']];
        yield 'disabled + error + required' => [
            ['disabled' => true, 'error' => true, 'required' => true],
            ['ids-input--disabled', 'ids-input--error', 'ids-input--required'],
        ];
    }

    public function testBooleanPropsAlsoAddNativeAttributes(): void
    {
        $crawler = $this->renderTwigComponent(InputText::class, [
            'disabled' => true,
            'required' => true,
        ])->crawler();

        $input = $this->getInput($crawler);
        self::assertNotNull($input->attr('disabled'), 'When disabled=true, the "disabled" attribute should be rendered.');
        self::assertNotNull($input->attr('required'), 'When required=true, the "required" attribute should be rendered.');
    }

    public function testAttributesBagMergesAndControlsValueAndWrapperDataAttr(): void
    {
        $crawler = $this->renderTwigComponent(InputText::class, [
            'attributes' => [
                'class' => 'extra-class',
                'placeholder' => 'foo',
                'data-custom' => 'custom',
                'data-ids-custom-init' => 'true',
                'value' => 'Hello',
            ],
        ])->crawler();

        $wrapper = $this->getWrapper($crawler);
        self::assertSame('true', $wrapper->attr('data-ids-custom-init'), 'Wrapper should receive data-ids-custom-init from attributes.');

        $input = $this->getInput($crawler);
        $class = (string) $input->attr('class');
        self::assertStringContainsString('extra-class', $class, 'Custom classes should be merged into input "class".');
        self::assertSame('foo', $input->attr('placeholder'), 'Placeholder should be rendered onto the input.');
        self::assertSame('custom', $input->attr('data-custom'), 'Custom data attribute should be rendered.');
        self::assertSame('Hello', $input->attr('value'), 'Value should be rendered onto the input.');
    }

    public function testClearActionVisibilityTogglesWithValue(): void
    {
        $crawler = $this->renderTwigComponent(InputText::class, [])->crawler();
        $this->assertClearActionHidden($crawler, true);

        $crawler = $this->renderTwigComponent(InputText::class, [
            'attributes' => ['value' => 'x'],
        ])->crawler();
        $this->assertClearActionHidden($crawler, false);
    }

    public function testInvalidTypeValueCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(InputText::class, ['type' => 'unsupported']);
    }

    public function testInvalidSizeValueCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(InputText::class, ['size' => 'giant']);
    }

    public function testInvalidBooleanTypesCauseResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(InputText::class, ['disabled' => 'yes']);
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function getInput(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-input-text__source > input')->first();
        self::assertGreaterThan(0, $node->count(), 'Input element not found under .ids-input-text__source.');

        return $node;
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function getWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('div.ids-input-text')->first();
        self::assertGreaterThan(0, $node->count(), 'Wrapper .ids-input-text not found.');

        return $node;
    }

    private function assertClearActionHidden(Crawler $crawler, bool $expectedHidden): void
    {
        $action = $crawler->filter('.ids-input-text__actions > div')->first();
        self::assertGreaterThan(0, $action->count(), 'Clear action container should be present.');

        $class = (string) $action->attr('class');
        if ($expectedHidden) {
            self::assertStringContainsString('ids-input-text__action--hidden', $class, 'Clear action should be hidden when value is empty.');
        } else {
            self::assertStringNotContainsString('ids-input-text__action--hidden', $class, 'Clear action should be visible when value is non-empty.');
        }
    }
}
