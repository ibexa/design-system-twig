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

#[AsTwigComponent]
final class HelperText
{
    public string $type = 'default';

    /**
     * @var array{default: 'info-circle', error: 'alert-error'}
     */
    private static array $iconMap = [
        'default' => 'info-circle',
        'error' => 'alert-error',
    ];

    /**
     * @param array<string, mixed> $props
     *
     * @return array<string, mixed>
     */
    #[PreMount]
    public function validate(array $props): array
    {
        $resolver = new OptionsResolver();
        $resolver->setIgnoreUndefined(true);
        $resolver
            ->define('type')
            ->allowedValues('default', 'error')
            ->default('default');

        return $resolver->resolve($props) + $props;
    }

    #[ExposeInTemplate('icon_name')]
    public function iconName(): string
    {
        return self::$iconMap[$this->type] ?? '';
    }
}
