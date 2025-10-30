<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * @phpstan-type ListItem array{
 *     id: non-empty-string,
 *     value: string|int,
 *     label: string,
 *     disabled?: bool,
 *     name?: string,
 *     required?: bool,
 *     attributes?: array<string, mixed>,
 *     label_attributes?: array<string, mixed>,
 *     inputWrapperClassName?: string,
 *     labelClassName?: string
 * }
 * @phpstan-type ListItems list<ListItem>
 */
trait ListFieldTrait
{
    public const string VERTICAL = 'vertical';
    public const string HORIZONTAL = 'horizontal';

    public string $direction = 'vertical';

    /** @var ListItems */
    #[ExposeInTemplate(name: 'items', getter: 'getItems')]
    public array $items = [];

    /**
     * @return ListItems
     */
    public function getItems(): array
    {
        return array_map(function (array $item): array {
            $listItem = $item + ['name' => $this->name, 'required' => $this->required];

            return $this->modifyListItem($listItem);
        }, $this->items);
    }

    /**
     * @param ListItem $item
     *
     * @return ListItem
     */
    protected function modifyListItem(array $item): array
    {
        return $item;
    }

    protected function validateListFieldProps(OptionsResolver $resolver): void
    {
        $resolver
            ->define('items')
            ->default([])
            ->allowedTypes('array');

        $resolver->setOptions('items', function (OptionsResolver $itemsResolver): void {
            $itemsResolver->setPrototype(true);
            $itemsResolver
                ->define('id')
                ->required()
                ->allowedTypes('string')
                ->allowedValues(static fn (string $value): bool => trim($value) !== '');

            $itemsResolver
                ->define('label')
                ->required()
                ->allowedTypes('string');

            $itemsResolver
                ->define('value')
                ->required()
                ->allowedTypes('string', 'int');

            $itemsResolver
                ->define('disabled')
                ->allowedTypes('bool');

            $itemsResolver
                ->define('attributes')
                ->allowedTypes('array');

            $itemsResolver
                ->define('label_attributes')
                ->allowedTypes('array');

            $itemsResolver
                ->define('inputWrapperClassName')
                ->allowedTypes('string');

            $itemsResolver
                ->define('labelClassName')
                ->allowedTypes('string');

            $itemsResolver
                ->define('name')
                ->allowedTypes('string');

            $itemsResolver
                ->define('required')
                ->allowedTypes('bool');

            $this->configureListFieldItemOptions($itemsResolver);
        });

        $resolver
            ->define('direction')
            ->allowedValues(self::VERTICAL, self::HORIZONTAL)
            ->default(self::VERTICAL);
    }

    protected function configureListFieldItemOptions(OptionsResolver $itemsResolver): void
    {
    }
}
