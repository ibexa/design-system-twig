<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent('ibexa:chip_dropdown')]
final class ChipDropdown extends AbstractDropdown
{
    public const string SIZE_MEDIUM = 'medium';

    public const string SIZE_SMALL = 'small';

    public bool $disabled = false;

    public ?string $icon = null;

    public ?string $label = null;

    public string $size = self::SIZE_MEDIUM;

    public string $value = '';

    #[PostMount]
    public function postMount(): void
    {
        if ($this->value === '') {
            $this->value = $this->getFlatItems()[0]['id'] ?? '';
        }
    }

    #[ExposeInTemplate('selected_label')]
    public function getSelectedLabel(): string
    {
        $selectedItem = array_find($this->getFlatItems(), fn (array $item): bool => $item['id'] === $this->value);

        return $selectedItem ? $selectedItem['label'] : '';
    }

    #[ExposeInTemplate('is_empty')]
    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    #[ExposeInTemplate('icon_size')]
    public function getIconSize(): string
    {
        return $this->size === self::SIZE_SMALL ? 'tiny' : 'tiny-small';
    }

    #[ExposeInTemplate('is_search_visible')]
    public function getIsSearchVisible(): bool
    {
        return false;
    }

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->define('disabled')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('icon')
            ->allowedTypes('null', 'string')
            ->default(null);
        $resolver
            ->define('label')
            ->allowedTypes('null', 'string')
            ->default(null);
        $resolver
            ->define('size')
            ->allowedValues(self::SIZE_MEDIUM, self::SIZE_SMALL)
            ->default(self::SIZE_MEDIUM);
        $resolver
            ->define('value')
            ->allowedTypes('string')
            ->default('');
    }
}
