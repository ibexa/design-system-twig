<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('ibexa:tag')]
final class Tag
{
    public string $size = 'medium';

    public string $type = 'primary';

    public string $icon = '';

    public string $isDark = '';

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
            ->define('size')
            ->allowedValues('small', 'medium')
            ->default('medium');
        $resolver
            ->define('type')
            ->allowedValues('primary', 'primary-alt', 'info', 'success', 'warning', 'error', 'neutral', 'icon-tag', 'success-ghost', 'error-ghost', 'neutral-ghost')
            ->default('primary');
        $resolver
            ->define('icon')
            ->allowedTypes('string');
        $resolver
            ->define('isDark')
            ->allowedTypes('bool');

        return $resolver->resolve($props) + $props;
    }
}
