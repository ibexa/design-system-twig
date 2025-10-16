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
        return array_map(function (array $item) {
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

        $resolver
            ->define('direction')
            ->allowedValues(self::VERTICAL, self::HORIZONTAL)
            ->default(self::VERTICAL);
    }
}
