<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components\InputText;

use Ibexa\DesignSystemTwig\Twig\Components\InputText\Field;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\PropertyAccess\Exception\InvalidTypeException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class FieldTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            Field::class,
            [
                'name' => 'title',
                'id' => 'title',
                'value' => 'Hello',
                'required' => true,
                'labelExtra' => ['class' => 'u-mb-1'],
                'helperTextExtra' => ['data-test' => 'help'],
                'input' => ['class' => 'ids-input u-w-full', 'placeholder' => 'Type…'],
            ]
        );

        self::assertInstanceOf(Field::class, $component, 'Component should mount as InputText.');

        self::assertSame('title', $component->name, 'Prop "name" should be set on the component.');
        self::assertSame('title', $component->id, 'Prop "id" should be set on the component.');
        self::assertSame('Hello', $component->value, 'Prop "value" should be set on the component.');
        self::assertTrue($component->required, 'Prop "required" should be true.');

        /** @var array<string, mixed> $label */
        $label = $component->getLabelExtra();
        self::assertSame('title', $label['for'] ?? null, 'Label "for" should match id.');
        self::assertTrue((bool)($label['required'] ?? false), 'Label should carry "required".');
        self::assertSame('u-mb-1', $label['class'] ?? null, 'Label class should be merged.');

        /** @var array<string, mixed> $input */
        $input = $component->getInput();
        self::assertSame('title', $input['id'] ?? null, 'Input "id" should be set from prop.');
        self::assertSame('title', $input['name'] ?? null, 'Input "name" should be set from prop.');
        self::assertTrue((bool)($input['required'] ?? false), 'Input should have required attribute.');
        self::assertSame('Hello', $input['value'] ?? null, 'Input "value" should pass through.');
        self::assertSame('true', $input['data-ids-custom-init'] ?? null, 'Input should include data-ids-custom-init="true".');
        self::assertSame('ids-input u-w-full', $input['class'] ?? null, 'Input class should be merged.');
        self::assertSame('Type…', $input['placeholder'] ?? null, 'Input placeholder should be merged.');
    }

    public function testDefaultRenderProducesInputWithCoreAttributes(): void
    {
        $rendered = $this->renderTwigComponent(
            Field::class,
            [
                'name' => 'email',
                'id' => 'email',
                'value' => 'test@example.com',
            ]
        );

        $crawler = $rendered->crawler();
        $input = $this->getInput($crawler);

        self::assertSame('email', $input->attr('id'), 'Rendered input "id" should equal the provided id.');
        self::assertSame('email', $input->attr('name'), 'Rendered input "name" should equal the provided name.');
        self::assertSame('test@example.com', $input->attr('value'), 'Rendered input "value" should equal the provided value.');
    }

    public function testRequiredAddsRequiredAttribute(): void
    {
        $rendered = $this->renderTwigComponent(
            Field::class,
            [
                'name' => 'username',
                'id' => 'username',
                'required' => true,
            ]
        );

        $input = $this->getInput($rendered->crawler());
        self::assertNotNull($input->attr('required'), 'Rendered input should have "required" attribute when required=true.');
    }

    public function testMergesCustomInputAttributes(): void
    {
        $rendered = $this->renderTwigComponent(
            Field::class,
            [
                'name' => 'slug',
                'id' => 'slug',
                'input' => [
                    'maxlength' => '64',
                    'autocomplete' => 'off',
                    'data-test' => 'slug',
                ],
            ]
        );

        $input = $this->getInput($rendered->crawler());

        self::assertSame('64', $input->attr('maxlength'), 'Input should include provided maxlength attribute.');
        self::assertSame('off', $input->attr('autocomplete'), 'Input should include provided autocomplete attribute.');
        self::assertSame('slug', $input->attr('data-test'), 'Input should include provided data-* attributes.');
    }

    /**
     * @param non-empty-string $option
     * @param array<string, mixed> $attrs
     */
    #[DataProvider('forbiddenAttributeProviders')]
    public function testForbiddenKeysThrow(string $option, array $attrs): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(
            Field::class,
            [
                'name' => 'foo',
                'id' => 'bar',
                $option => $attrs,
            ]
        );
    }

    /**
     * @return iterable<string, array{option: string, attrs: array<string, mixed>}>
     */
    public static function forbiddenAttributeProviders(): iterable
    {
        yield 'labelExtra: for' => ['option' => 'labelExtra', 'attrs' => ['for' => 'foo']];
        yield 'labelExtra: required' => ['option' => 'labelExtra', 'attrs' => ['required' => true]];
        yield 'input: id' => ['option' => 'input', 'attrs' => ['id' => 'foo']];
        yield 'input: name' => ['option' => 'input', 'attrs' => ['name' => 'foo']];
        yield 'input: required' => ['option' => 'input', 'attrs' => ['required' => true]];
        yield 'input: value' => ['option' => 'input', 'attrs' => ['value' => 'foo']];
    }

    public function testMissingRequiredPropsCauseResolverError(): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->mountTwigComponent(Field::class, ['name' => 'only_name']);
    }

    public function testInvalidTypesCauseResolverError(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->mountTwigComponent(Field::class, [
            'name' => 'foo',
            'id' => 'bar',
            'required' => 'yes',
        ]);
    }

    private function getInput(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('.ids-input-text__source > input')->first();
        self::assertGreaterThan(0, $node->count(), 'Input element not found under .ids-input-text__source.');

        return $node;
    }
}
