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

#[AsTwigComponent('ibexa:tag')]
final class Tag
{
    public string $size = 'medium';

    public string $type = 'primary';

    public bool $isDark = false;

    public string $icon = '';

    /** @var string[] */
    private static array $ghostTypes = ['success-ghost', 'error-ghost', 'neutral-ghost'];

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
            ->allowedValues('primary', 'primary-alt', 'info', 'success', 'warning', 'error', 'neutral', 'icon-tag', ...self::$ghostTypes);
        $resolver
            ->define('icon')
            ->allowedTypes('string');
        $resolver
            ->define('isDark')
            ->allowedTypes('bool')
            ->default(false);

        return $resolver->resolve($props) + $props;
    }

    #[ExposeInTemplate('is_ghost_type')]
    public function isGhostType() : bool
    {
        return in_array($this->type, self::$ghostTypes);
    }
}
