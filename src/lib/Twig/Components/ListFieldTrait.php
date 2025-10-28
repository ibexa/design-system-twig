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

        $resolver->setOptions('items', static function (OptionsResolver $itemsResolver): void {
            $itemsResolver->setPrototype(true);
            $itemsResolver
                ->setRequired(['id', 'label', 'value'])
                ->setAllowedTypes('id', 'string')
                ->setAllowedValues('id', static fn (string $value): bool => trim($value) !== '')
                ->setAllowedTypes('label', 'string')
                ->setAllowedTypes('value', ['string', 'int']);

            $itemsResolver->setDefined([
                'disabled',
                'attributes',
                'label_attributes',
                'inputWrapperClassName',
                'labelClassName',
                'name',
                'required',
            ]);

            $itemsResolver->setAllowedTypes('disabled', 'bool');
            $itemsResolver->setAllowedTypes('attributes', 'array');
            $itemsResolver->setAllowedTypes('label_attributes', 'array');
            $itemsResolver->setAllowedTypes('inputWrapperClassName', 'string');
            $itemsResolver->setAllowedTypes('labelClassName', 'string');
            $itemsResolver->setAllowedTypes('name', 'string');
            $itemsResolver->setAllowedTypes('required', 'bool');
        });

        $resolver
            ->define('direction')
            ->allowedValues(self::VERTICAL, self::HORIZONTAL)
            ->default(self::VERTICAL);
    }
}
