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

#[AsTwigComponent('ibexa:alert')]
final class Alert
{
    private const ICONS_TYPE_MAP = [
        'success' => 'check-circle',
        'warning' => 'alert-warning',
        'error' => 'alert-error',
        'info' => 'info-rounded',
    ];

    private const ROLES_TYPE_MAP = [
        'success' => 'status',
        'warning' => 'alert',
        'error' => 'alert',
        'info' => 'status',
    ];

    public string $type;

    public string $variant = 'floating';

    public string $title = '';

    public string $icon = '';

    #[ExposeInTemplate('icon_path')]
    public string $iconPath = '';

    #[ExposeInTemplate('is_dismissible')]
    public bool $isDismissible = false;

    public string $role = '';

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
            ->define('type')
            ->required()
            ->allowedValues(...array_keys(self::ICONS_TYPE_MAP));
        $resolver
            ->define('variant')
            ->allowedValues('floating', 'local', 'toast')
            ->default('floating');
        $resolver
            ->define('title')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('icon')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('iconPath')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('isDismissible')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('role')
            ->allowedValues('alert', 'status');

        return $resolver->resolve($props) + $props;
    }

    #[ExposeInTemplate('icon_name')]
    public function getIconName(): string
    {
        return $this->icon !== '' ? $this->icon : self::ICONS_TYPE_MAP[$this->type];
    }

    #[ExposeInTemplate('resolved_role')]
    public function getResolvedRole(): string
    {
        return $this->role !== '' ? $this->role : self::ROLES_TYPE_MAP[$this->type];
    }
}
