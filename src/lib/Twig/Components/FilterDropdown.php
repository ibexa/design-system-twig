<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * @phpstan-import-type TDropdownItem from AbstractDropdown
 */
#[AsTwigComponent('ibexa:filter_dropdown')]
final class FilterDropdown extends AbstractDropdown
{
    public const string TYPE_DEFAULT = 'default';
    public const string TYPE_DASHBOARD = 'dashboard';
    public const string TYPE_MORE_FILTERS = 'more-filters';
    public const string TYPE_MORE_FILTERS_SMALL = 'more-filters-small';
    public const array TYPES = [self::TYPE_DEFAULT, self::TYPE_DASHBOARD, self::TYPE_MORE_FILTERS, self::TYPE_MORE_FILTERS_SMALL];

    public const string SUMMARY_NONE = 'none';
    public const string SUMMARY_COUNTER = 'counter';
    public const string SUMMARY_VALUE = 'value';

    public string $label;

    public string $type = self::TYPE_DEFAULT;

    /** @var array<string> */
    public array $value = [];

    public bool $hasSearch = true;

    /**
     * @return list<TDropdownItem>
     */
    public function getSelectedItems(): array
    {
        return $this->filterItemsByIds($this->value);
    }

    #[ExposeInTemplate('is_empty')]
    public function isEmpty(): bool
    {
        return count($this->getSelectedItems()) === 0;
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
        $resolver->setAllowedValues('disabled', false);
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
            ->default([])
            ->normalize(self::normalizeValue(...));
        $resolver
            ->define('hasSearch')
            ->allowedTypes('bool')
            ->default(true);
    }

    /**
     * @param Options<array<string, mixed>> $options
     * @param array<mixed> $value
     *
     * @return array<int, string>
     */
    private static function normalizeValue(
        Options $options,
        array $value
    ): array {
        foreach ($value as $index => $id) {
            if (!is_int($id) && !is_string($id)) {
                throw new InvalidOptionsException(
                    sprintf('Each filter dropdown value must be an int or a string, "%s" given at index %s.', get_debug_type($id), $index)
                );
            }
        }

        return array_map(strval(...), array_values($value));
    }
}
