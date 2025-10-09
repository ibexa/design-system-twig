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
 */
trait ListFieldTrait
{
    public string $direction = 'vertical';

    /** @var ListItem */
    #[ExposeInTemplate(name: 'items', getter: 'getItems')]
    public array $items = [];

    /** @return CheckboxItems */
    public function getItems(): array
    {
        return array_map(function ($item) {
            $listItem = $item + ['name' => $this->name, 'required' => $this->required];

            return $this->modifyListItem($listItem);
        }, $this->items);
    }

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
            ->allowedValues('vertical', 'horizontal')
            ->default('vertical');
    }
}
