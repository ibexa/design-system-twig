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

        $resolver->setOptions('items', static function (OptionsResolver $itemsResolver): void {
            $itemsResolver->setPrototype(true);

            $itemsResolver
                ->define('id')
                ->required()
                ->allowedTypes('string')
                ->allowedValues(static fn (string $value): bool => trim($value) !== '');

            $itemsResolver
                ->define('value')
                ->required()
                ->allowedTypes('string', 'int');

            $itemsResolver
                ->define('label')
                ->required()
                ->allowedTypes('string');

            $itemsResolver
                ->define('disabled')
                ->allowedTypes('bool')
                ->default(false);

            $itemsResolver
                ->define('tileClass')
                ->allowedTypes('string')
                ->default('');

            $itemsResolver
                ->define('attributes')
                ->allowedTypes('array')
                ->default([]);
        });

        $resolver->setDefaults(['direction' => 'horizontal']);
        $resolver->setDefaults(['value' => '']);
        $resolver->setAllowedTypes('value', 'string');
    }
}
