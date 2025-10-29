<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\Checkbox;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractField;
use Ibexa\DesignSystemTwig\Twig\Components\ListFieldTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * @phpstan-type CheckboxItem array{
 *     id: non-empty-string,
 *     value: string|int,
 *     label: string,
 *     disabled?: bool,
 *     name?: string,
 *     required?: bool,
 *     attributes?: array<string, mixed>,
 *     label_attributes?: array<string, mixed>,
 *     inputWrapperClassName?: string,
 *     labelClassName?: string,
 *     checked?: bool
 * }
 * @phpstan-type CheckboxItems list<CheckboxItem>
 */
#[AsTwigComponent('ibexa:checkbox:list_field')]
final class ListField extends AbstractField
{
    use ListFieldTrait;

    /** @var array<string|int> */
    public array $value = [];

    /**
     * @param CheckboxItem $item
     *
     * @return CheckboxItem
     */
    protected function modifyListItem(array $item): array
    {
        $item['checked'] = in_array($item['value'], $this->value, true);

        return $item;
    }

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $this->validateListFieldProps($resolver);

        // TODO: check if items are valid according to Checkbox/Field component
        $resolver->setDefaults(['value' => []]);
        $resolver->setAllowedTypes('value', 'array');
    }
}
