<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\inputs;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent]
final class AltRadio
{
    public string $label = '';
    
    #[ExposeInTemplate('input_props')]
    public array $inputProps = [];

    #[PreMount]
    public function validate(array $props): array
    {
        $resolver = new OptionsResolver();
        $resolver->setIgnoreUndefined();
        $resolver
            ->define('label')
            ->required()
            ->allowedTypes('string')
            ->default('');
        $resolver->setOptions('inputProps', function (OptionsResolver $inputPropsResolver): void {
            $inputPropsResolver->setIgnoreUndefined();
            $inputPropsResolver
                ->define('name')
                ->required()
                ->allowedTypes('string')
                ->default('');
            $inputPropsResolver
                ->define('checked')
                ->allowedTypes('bool')
                ->default(false);
            $inputPropsResolver
                ->define('disabled')
                ->allowedTypes('bool')
                ->default(false);
            $inputPropsResolver
                ->define('error')
                ->allowedTypes('bool')
                ->default(false);
        });

        // for Mikołaj - not sure how to do it, but extra props in inputProps aren't there in attributes
        // I tried to concat recursively with array_replace_recursive($resolver->resolve($props), $props);
        // but then extra props are in input_props array instead of attributes->input_props :/
        return $resolver->resolve($props);
    }
}
