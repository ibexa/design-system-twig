<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Symfony\UX\TwigComponent\Attribute\PostMount;

abstract class AbstractButton
{
    public string $size = 'medium';
    public string $type = 'primary';
    public bool $disabled = false;
    public string $icon = '';

    private static $iconSizeMap = [
        'small' => 'tiny-small',
        'medium' => 'small',
    ];

    /**
     * @param array<string, mixed> $props
     */
    #[PreMount]
    public function validate(array $props): array
    {
        $resolver = new OptionsResolver();
        $resolver->setIgnoreUndefined(true);
        $resolver
            ->define('size')
            ->allowedValues('small', 'medium')
            ->default('medium');
        $resolver
            ->define('type')
            ->allowedValues('primary', 'secondary', 'tertiary', 'secondary-alt', 'tertiary-alt')
            ->default('primary');
        $resolver
            ->define('disabled')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('icon')
            ->allowedTypes('string');

        $this->configurePropsResolver($resolver);

        return $resolver->resolve($props) + $props;
    }

    #[PostMount]
    public function setIconSize(): void
    {
        $this->icon_size = self::$iconSizeMap[$this->size];
    }

    abstract protected function configurePropsResolver(OptionsResolver $resolver): void;
}
