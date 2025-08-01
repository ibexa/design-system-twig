<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\formControls;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent]
final class InputText
{
    // public array $input = [];
    public array $label_extra = [];

    /**
     * @param array<string, mixed> $props
     */
    #[PreMount]
    public function validate(array $props): array
    {
        $resolver = new OptionsResolver();
        $resolver->setIgnoreUndefined(true);
        $resolver->setOptions('label_extra', function (OptionsResolver $labelExtraResolver): void {
            $labelExtraResolver->setIgnoreUndefined(true);
        });
        // $resolver
        //     ->define('type')
        //     ->allowedValues('text', 'password', 'email', 'number', 'tel', 'search', 'url')
        //     ->default('text');
        // $resolver
        //     ->define('size')
        //     ->allowedValues('small', 'medium')
        //     ->default('medium');
        // $resolver
        //     ->define('disabled')
        //     ->allowedTypes('bool')
        //     ->default(false);
        // $resolver
        //     ->define('error')
        //     ->allowedTypes('bool')
        //     ->default(false);
        // $resolver
        //     ->define('required')
        //     ->allowedTypes('bool')
        //     ->default(false);

        return $resolver->resolve($props) + $props;
    }
}
