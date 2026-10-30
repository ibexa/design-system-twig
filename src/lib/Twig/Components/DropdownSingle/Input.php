<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\DropdownSingle;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractDropdownInput;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent('ibexa:dropdown_single:input')]
final class Input extends AbstractDropdownInput
{
    public const string TYPE_INPUT = 'input';

    public const string TYPE_CHIP = 'chip';

    public ?string $value = '';

    public string $type = self::TYPE_INPUT;

    public ?string $icon = null;

    public ?string $label = null;

    #[PostMount]
    public function postMount(): void
    {
        if (empty($this->value)) {
            $this->value = $this->getFlatItems()[0]['id'] ?? '';
        }
    }

    #[ExposeInTemplate('selected_label')]
    public function getSelectedLabel(): string
    {
        $value = $this->value ?? '';
        $selected_item = array_find($this->getFlatItems(), static function (array $item) use ($value): bool {
            return $item['id'] === $value;
        });

        return $selected_item ? $selected_item['label'] : '';
    }

    #[ExposeInTemplate('is_empty')]
    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    public function isChip(): bool
    {
        return $this->type === self::TYPE_CHIP;
    }

    #[ExposeInTemplate('icon_size')]
    public function getIconSize(): string
    {
        return $this->size === self::SIZE_SMALL ? 'tiny' : 'tiny-small';
    }

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        parent::configurePropsResolver($resolver);

        $resolver
            ->define('value')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('type')
            ->allowedValues(self::TYPE_INPUT, self::TYPE_CHIP)
            ->default(self::TYPE_INPUT);
        $resolver
            ->define('icon')
            ->allowedTypes('null', 'string')
            ->default(null);
        $resolver
            ->define('label')
            ->allowedTypes('null', 'string')
            ->default(null);
    }
}
