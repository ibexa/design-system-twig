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
 *     value: string|int,
 *     label: string,
 *     disabled?: bool
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

        // TODO: check if items have value and label component

        $resolver->setDefaults(['direction' => 'horizontal']);
        $resolver->setDefaults(['value' => '']);
        $resolver->setAllowedTypes('value', 'string');
    }
}
