<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\Tests\Integration\DesignSystemTwig\Twig\Stub\DummyListFieldComponent;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

final class ListFieldTraitTest extends TestCase
{
    public function testResolveWithValidItemsAndHorizontalDirectionSucceeds(): void
    {
        $resolved = $this->getComponent()->resolve([
            'items' => [
                ['id' => 'a', 'label' => 'Alpha', 'value' => 'A'],
                ['id' => 'b', 'label' => 'Beta',  'value' => 'B'],
            ],
            'direction' => 'horizontal',
        ]);

        self::assertArrayHasKey(
            'items',
            $resolved,
            'Resolved options should contain "items".'
        );
        self::assertCount(
            2,
            $resolved['items'],
            '"items" should contain two entries.'
        );
        self::assertSame(
            'horizontal',
            $resolved['direction'] ?? null,
            '"direction" should resolve to HORIZONTAL.'
        );
    }

    public function testDefaultsWhenNoOptionsProvided(): void
    {
        $resolved = $this->getComponent()->resolve([]);

        self::assertSame(
            [],
            $resolved['items'],
            '"items" should default to an empty array.'
        );
        self::assertSame(
            'vertical',
            $resolved['direction'],
            '"direction" should default to VERTICAL.'
        );
    }

    public function testItemMissingValueCausesResolverError(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->getComponent()->resolve([
            'items' => [
                ['label' => 'Alpha'],
            ],
        ]);
    }

    public function testItemMissingIdCausesResolverError(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->getComponent()->resolve([
            'items' => [
                ['label' => 'Alpha', 'value' => 'A'],
            ],
        ]);
    }

    public function testItemMissingLabelCausesResolverError(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->getComponent()->resolve([
            'items' => [
                ['value' => 'A'],
            ],
        ]);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @dataProvider invalidItemOptionsProvider
     */
    public function testInvalidItemOptionsCauseResolverError(array $options): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->getComponent()->resolve($options);
    }

    /**
     * @return iterable<string, array{0: array<string, mixed>}>
     */
    public static function invalidItemOptionsProvider(): iterable
    {
        yield 'invalid label/value types' => [
            [
                'items' => [
                    ['id' => 'a', 'label' => 123, 'value' => new stdClass()],
                ],
            ],
        ];

        yield 'empty id' => [
            [
                'items' => [
                    ['id' => '   ', 'label' => 'Alpha', 'value' => 'A'],
                ],
            ],
        ];

        yield 'invalid disabled type' => [
            [
                'items' => [
                    ['id' => 'a', 'label' => 'Alpha', 'value' => 'A', 'disabled' => 'yes'],
                ],
            ],
        ];

        yield 'invalid attributes type' => [
            [
                'items' => [
                    ['id' => 'a', 'label' => 'Alpha', 'value' => 'A', 'attributes' => 'oops'],
                ],
            ],
        ];

        yield 'invalid label_attributes type' => [
            [
                'items' => [
                    ['id' => 'a', 'label' => 'Alpha', 'value' => 'A', 'label_attributes' => 'oops'],
                ],
            ],
        ];

        yield 'invalid input wrapper class name' => [
            [
                'items' => [
                    ['id' => 'a', 'label' => 'Alpha', 'value' => 'A', 'inputWrapperClassName' => ['not-a-string']],
                ],
            ],
        ];

        yield 'invalid label class name' => [
            [
                'items' => [
                    ['id' => 'a', 'label' => 'Alpha', 'value' => 'A', 'labelClassName' => ['not-a-string']],
                ],
            ],
        ];

        yield 'invalid name type' => [
            [
                'items' => [
                    ['id' => 'a', 'label' => 'Alpha', 'value' => 'A', 'name' => ['array']],
                ],
            ],
        ];

        yield 'invalid required type' => [
            [
                'items' => [
                    ['id' => 'a', 'label' => 'Alpha', 'value' => 'A', 'required' => 'yes'],
                ],
            ],
        ];
    }

    public function testInvalidItemsTypeCausesResolverError(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->getComponent()->resolve([
            'items' => 'not-an-array',
        ]);
    }

    public function testInvalidDirectionValueCausesResolverError(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->getComponent()->resolve([
            'items' => [['id' => 'a', 'label' => 'Alpha', 'value' => 'A']],
            'direction' => 'diagonal',
        ]);
    }

    private function getComponent(): DummyListFieldComponent
    {
        return new DummyListFieldComponent();
    }
}
