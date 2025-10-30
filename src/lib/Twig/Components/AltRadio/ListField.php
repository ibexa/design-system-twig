<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\AltRadio;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractField;
use Ibexa\DesignSystemTwig\Twig\Components\ListFieldTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * @phpstan-type AltRadioItem array{
 *     id: non-empty-string,
 *     value: string|int,
 *     label: string,
 *     disabled?: bool,
 *     tileClass?: string,
 *     attributes?: array<string, mixed>,
 *     label_attributes?: array<string, mixed>,
 *     inputWrapperClassName?: string,
 *     labelClassName?: string,
 *     name?: string,
 *     required?: bool
 * }
 * @phpstan-type AltRadioItems list<AltRadioItem>
 */
#[AsTwigComponent('ibexa:alt_radio:list_field')]
final class ListField extends AbstractField
{
    use ListFieldTrait;

    public string $value = '';

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $this->validateListFieldProps($resolver);

        $resolver->setDefault('direction', self::HORIZONTAL);
        $resolver->setDefault('value', '');
        $resolver->setAllowedTypes('value', 'string');
    }

    protected function configureListFieldItemOptions(OptionsResolver $itemsResolver): void
    {
        $itemsResolver
            ->define('tileClass')
            ->allowedTypes('string')
            ->default('');

        $itemsResolver->setDefault('disabled', false);
        $itemsResolver->setDefault('attributes', []);
    }
}
