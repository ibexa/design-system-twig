<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\Inputs;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('ibexa:inputs:alt_radio')]
final class AltRadio extends AbstractChoiceInput
{
    public string $label;

    #[ExposeInTemplate('tile_class')]
    public string $tileClass = '';

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->define('label')
            ->required()
            ->allowedTypes('string');
        $resolver
            ->define('tileClass')
            ->allowedTypes('string')
            ->default('');
    }
}
