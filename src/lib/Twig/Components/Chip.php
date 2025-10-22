<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('ibexa:chip')]
final class Chip
{
    public bool $error = false;

    #[ExposeInTemplate('is_deletable')]
    public bool $isDeletable = true;

    public bool $disabled = false;

    /**
     * @param array<string, mixed> $props
     *
     * @return array<string, mixed>
     */
    #[PreMount]
    public function validate(array $props): array
    {
        $resolver = new OptionsResolver();
        $resolver->setIgnoreUndefined();
        $resolver
            ->define('error')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('isDeletable')
            ->allowedTypes('bool')
            ->default(true);
        $resolver
            ->define('disabled')
            ->allowedTypes('bool')
            ->default(false);

        return $resolver->resolve($props) + $props;
    }
}
