<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\DropdownMulti;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractDropdown;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('ibexa:dropdown_multi:input')]
final class Input extends AbstractDropdown
{
    /** @var array<string> */
    public array $value = [];

    /**
     * @return array<int, array{id: string, label: string}|null>
     */
    #[ExposeInTemplate('selected_items')]
    public function getSelectedItems(): array
    {
        $items = $this->items;

        return array_map(
            static function (string $id) use ($items): ?array {
                return array_find($items, static function (array $item) use ($id): bool {
                    return $item['id'] === $id;
                });
            },
            $this->value
        );
    }

    #[ExposeInTemplate('is_empty')]
    public function isEmpty(): bool
    {
        return count($this->value) === 0;
    }

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->define('value')
            ->allowedTypes('array')
            ->default([]);
    }
}
