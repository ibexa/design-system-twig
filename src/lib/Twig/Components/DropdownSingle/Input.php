<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\DropdownSingle;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractDropdown;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('ibexa:dropdown_single:input')]
final class Input extends AbstractDropdown
{
    public ?string $value = '';

    #[ExposeInTemplate('selected_label')]
    public function getSelectedLabel(): string
    {
        $value = $this->value ?? '';
        $selected_item = array_find($this->items, static function (array $item) use ($value) {
            return $item['id'] === $value;
        });

        return $selected_item ? $selected_item['label'] : '';
    }

    #[ExposeInTemplate('is_empty')]
    public function isEmpty() : bool
    {
        return $this->value === '';
    }

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->define('value')
            ->allowedTypes('string')
            ->default('');
    }
}
