<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\Tests\Integration\DesignSystemTwig\Twig\Stub\DummyListFieldComponent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

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

        self::assertArrayHasKey('items', $resolved, 'Resolved options should contain "items".');
        self::assertCount(2, $resolved['items'], '"items" should contain two entries.');
        self::assertSame('horizontal', $resolved['direction'] ?? null, '"direction" should resolve to HORIZONTAL.');
    }

    public function testDefaultsWhenNoOptionsProvided(): void
    {
        $resolved = $this->getComponent()->resolve([]);

        self::assertSame([], $resolved['items'], '"items" should default to an empty array.');
        self::assertSame('vertical', $resolved['direction'], '"direction" should default to VERTICAL.');
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
