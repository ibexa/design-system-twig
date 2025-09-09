<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\Inputs;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ibexa:inputs:three_state_checkbox')]
final class ThreeStateCheckbox extends AbstractChoiceInput
{
    public bool $indeterminate = false;

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->define('indeterminate')
            ->allowedTypes('bool')
            ->default(false);
    }
}
