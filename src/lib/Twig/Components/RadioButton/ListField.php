<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\RadioButton;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractField;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * @phpstan-type RadioButtonItem array{
 *     value: string|int,
 *     label: string,
 *     disabled?: bool
 * }
 * @phpstan-type RadioButtonItems list<RadioButtonItem>
 */
#[AsTwigComponent('ibexa:radio_button:list_field')]
final class ListField extends AbstractField
{
    public string $direction = 'vertical';

    /** @var RadioButtonItems */
    #[ExposeInTemplate(name: 'items', getter: 'getItems')]
    public array $items = [];

    /** @return RadioButtonItems */
    public function getItems(): array
    {
        return array_map(function ($item) {
            return $item + ['name' => $this->name, 'required' => $this->required];
        }, $this->items);
    }

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'items' => [],
        ]);
        $resolver->setAllowedTypes('items', 'array');
        // TODO: check if items are valid according to RadioButton/Field component

        $resolver
            ->define('direction')
            ->allowedValues('vertical', 'horizontal')
            ->default('vertical');
    }
}
