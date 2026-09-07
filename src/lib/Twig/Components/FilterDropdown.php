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

/**
 * @phpstan-import-type TDropdownItem from AbstractDropdown
 */
#[AsTwigComponent('ibexa:filter_dropdown')]
final class FilterDropdown extends AbstractDropdown
{
    public const TYPE_DEFAULT = 'default';
    public const TYPE_DASHBOARD = 'dashboard';
    public const TYPE_MORE_FILTERS = 'more-filters';
    public const TYPE_MORE_FILTERS_SMALL = 'more-filters-small';
    public const TYPES = [self::TYPE_DEFAULT, self::TYPE_DASHBOARD, self::TYPE_MORE_FILTERS, self::TYPE_MORE_FILTERS_SMALL];

    public const SUMMARY_NONE = 'none';
    public const SUMMARY_COUNTER = 'counter';
    public const SUMMARY_VALUE = 'value';

    public string $label;

    public string $type = self::TYPE_DEFAULT;

    /** @var array<string> */
    public array $value = [];

    #[ExposeInTemplate('has_search')]
    public bool $hasSearch = true;

    /**
     * @return list<TDropdownItem>
     */
    #[ExposeInTemplate('selected_items')]
    public function getSelectedItems(): array
    {
        return array_values(array_filter(
            $this->getFlatItems(),
            fn (array $item): bool => in_array($item['id'], $this->value, true)
        ));
    }

    #[ExposeInTemplate('is_empty')]
    public function isEmpty(): bool
    {
        return count($this->value) === 0;
    }

    /**
     * @return array{mode: string, text: string}
     */
    #[ExposeInTemplate('summary')]
    public function getSummary(): array
    {
        $selectedItems = $this->getSelectedItems();
        $count = count($selectedItems);

        if ($count === 0) {
            return ['mode' => self::SUMMARY_NONE, 'text' => ''];
        }

        if ($this->type === self::TYPE_DASHBOARD && $count === 1) {
            return ['mode' => self::SUMMARY_VALUE, 'text' => $selectedItems[0]['label']];
        }

        return ['mode' => self::SUMMARY_COUNTER, 'text' => (string) $count];
    }

    #[ExposeInTemplate('is_search_visible')]
    public function getIsSearchVisible(): bool
    {
        return $this->hasSearch;
    }

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->define('label')
            ->required()
            ->allowedTypes('string');
        $resolver
            ->define('type')
            ->allowedValues(...self::TYPES)
            ->default(self::TYPE_DEFAULT);
        $resolver
            ->define('value')
            ->allowedTypes('array')
            ->default([]);
        $resolver
            ->define('hasSearch')
            ->allowedTypes('bool')
            ->default(true);
    }
}
