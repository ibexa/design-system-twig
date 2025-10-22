<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * @phpstan-type ListItem array{
 *     value: string|int,
 *     label: string,
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
        $resolver->setDefaults([
            'items' => [],
        ]);
        $resolver->setAllowedTypes('items', 'array');
        $resolver->setAllowedValues('items', static function (array $items): bool {
            foreach ($items as $index => $item) {
                if (!is_array($item)) {
                    throw new InvalidOptionsException(
                        sprintf('Item at index %d must be an array, %s given.', $index, get_debug_type($item))
                    );
                }

                if (!array_key_exists('value', $item)) {
                    throw new InvalidOptionsException(
                        sprintf('Item at index %d must define a "value" key.', $index)
                    );
                }

                if (!array_key_exists('label', $item)) {
                    throw new InvalidOptionsException(
                        sprintf('Item at index %d must define a "label" key.', $index)
                    );
                }

                if (!is_string($item['label'])) {
                    throw new InvalidOptionsException(
                        sprintf('Item at index %d must define a "label" string, %s given.', $index, get_debug_type($item['label']))
                    );
                }

                if (!is_string($item['value']) && !is_int($item['value'])) {
                    throw new InvalidOptionsException(
                        sprintf('Item at index %d must define a "value" as string or int, %s given.', $index, get_debug_type($item['value']))
                    );
                }
            }

            return true;
        });

        $resolver
            ->define('direction')
            ->allowedValues(self::VERTICAL, self::HORIZONTAL)
            ->default(self::VERTICAL);
    }
}
