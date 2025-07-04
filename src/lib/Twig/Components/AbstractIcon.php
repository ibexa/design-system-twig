<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

abstract class AbstractIcon
{
    public string $size = 'medium';

    /** @var string[] */
    public array $classes = [];

    /**
     * @param array<string, mixed> $props
     */
    #[PreMount]
    public function validate(array $props): array
    {
        $resolver = new OptionsResolver();
        $resolver->define('classes')->allowedTypes('array')->default([]);
        $resolver
            ->define('size')
            ->allowedValues('tiny', 'tiny-small', 'small', 'small-medium', 'medium', 'medium-large', 'large', 'extra-large')
            ->default('medium');

        $this->configurePropsResolver($resolver);

        return $resolver->resolve($props);
    }

    abstract protected function configurePropsResolver(OptionsResolver $resolver): void;
}
