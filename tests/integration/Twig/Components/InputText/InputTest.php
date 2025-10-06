<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\InputText;

use Generator;
use Ibexa\DesignSystemTwig\Twig\Components\InputText\Input;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class InputTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(Input::class, [
            'type' => 'email',
            'size' => 'small',
            'disabled' => true,
            'error' => true,
            'required' => true,
        ]);

        self::assertInstanceOf(Input::class, $component, 'Component should mount as InputText\\Input.');
    }

    public function testDefaultRenderProducesWrapperAndInputWithBaseClasses(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [])->crawler();

        $wrapper = $this->getWrapper($crawler);
        self::assertSame('div', $wrapper->nodeName(), 'Wrapper should be a <div>.');

        $input = $this->getInput($crawler);
        $class = $this->getClassAttr($input);

        self::assertStringContainsString('ids-input', $class, 'Base class "ids-input" should be present.');
        self::assertStringContainsString('ids-input--text', $class, 'Default type should add "ids-input--text".');
        self::assertStringContainsString('ids-input--medium', $class, 'Default size should add "ids-input--medium".');

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
        $crawler = $this->renderTwigComponent(Input::class, $props)->crawler();
        $input = $this->getInput($crawler);
        $class = $this->getClassAttr($input);

        foreach ($expectedClasses as $cls) {
            self::assertStringContainsString($cls, $class, sprintf('Expected class "%s" should be present.', $cls));
        }
    }

    public static function variantProvider(): Generator
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
        $crawler = $this->renderTwigComponent(Input::class, [
            'disabled' => true,
            'required' => true,
        ])->crawler();

        $input = $this->getInput($crawler);

        self::assertNotNull($input->attr('disabled'), 'When disabled=true, the "disabled" attribute should be rendered.');
        self::assertNotNull($input->attr('required'), 'When required=true, the "required" attribute should be rendered.');
    }

    public function testAttributesBagMergesAndWrapperCustomInit(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [
            'attributes' => [
                'class' => 'extra-class',
                'placeholder' => 'Type…',
                'data-qa' => 'ipt',
                'data-ids-custom-init' => '1',
                'value' => 'Hello',
            ],
        ])->crawler();

        $wrapper = $this->getWrapper($crawler);
        self::assertNotNull($wrapper->attr('data-ids-custom-init'), 'Wrapper should include data-ids-custom-init when provided in attributes.');

        $input = $this->getInput($crawler);
        $class = $this->getClassAttr($input);

        self::assertStringContainsString('extra-class', $class, 'Custom classes should be merged into input "class".');
        self::assertSame('Type…', $input->attr('placeholder'), 'Placeholder should be rendered onto the input.');
        self::assertSame('ipt', $input->attr('data-qa'), 'Custom data attribute should be rendered.');
        self::assertSame('Hello', $input->attr('value'), 'Value should be rendered onto the input.');
    }

    public function testClearActionVisibilityTogglesWithValue(): void
    {
        $crawler = $this->renderTwigComponent(Input::class, [])->crawler();
        $this->assertClearActionHidden($crawler, true);

        $crawler = $this->renderTwigComponent(Input::class, [
            'attributes' => ['value' => 'x'],
        ])->crawler();
        $this->assertClearActionHidden($crawler, false);
    }

    public function testInvalidTypeValueCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Input::class, ['type' => 'unsupported']);
    }

    public function testInvalidSizeValueCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Input::class, ['size' => 'giant']);
    }

    public function testInvalidBooleanTypesCauseResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Input::class, ['disabled' => 'yes']);
    }

    private function getWrapper(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('div.ids-input-text')->first();
        self::assertGreaterThan(0, $node->count(), 'Wrapper .ids-input-text should be present.');

        return $node;
    }

    private function getInput(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-input-text__source > input')->first();
        self::assertGreaterThan(0, $node->count(), 'Input element not found under .ids-input-text__source.');

        return $node;
    }

    private function assertClearActionHidden(Crawler $crawler, bool $expectedHidden): void
    {
        $action = $crawler->filter('.ids-input-text__actions > div')->first();
        self::assertGreaterThan(0, $action->count(), 'Clear action container should be present.');

        $class = $this->getClassAttr($action);
        if ($expectedHidden) {
            self::assertStringContainsString('ids-input-text__action--hidden', $class, 'Clear action should be hidden when value is empty.');
        } else {
            self::assertStringNotContainsString('ids-input-text__action--hidden', $class, 'Clear action should be visible when value is non-empty.');
        }
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }
}
