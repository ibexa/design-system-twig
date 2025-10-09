<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\RadioButton;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractField;
use Ibexa\DesignSystemTwig\Twig\Components\ListFieldTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

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
    use ListFieldTrait;

    public string $value = '';

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $this->validateListFieldProps($resolver);

        // TODO: check if items are valid according to RadioButton/Field component

        $resolver->setDefaults(['value' => '']);
        $resolver->setAllowedTypes('value', 'string');
    }
}
