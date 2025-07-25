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
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent(
    name: 'ibexa:HelperText',
    template: '@ibexadesign/design_system/components/helper_text.html.twig'
)]
final class HelperText
{
    public string $type = 'default';
    public string $icon_name = '';

    private static $iconMap = [
        'default' => 'info-circle',
        'error' => 'alert-error',
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
            ->define('type')
            ->allowedValues('default', 'error')
            ->default('default');

        return $resolver->resolve($props) + $props;
    }

    #[PostMount]
    public function setIconName(): void
    {
        $this->icon_name = self::$iconMap[$this->type];
    }
}
