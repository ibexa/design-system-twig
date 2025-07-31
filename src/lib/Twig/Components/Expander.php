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
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
final class Expander
{
    public string $type;
    public bool $is_expanded = false;
    public string $expand_label = '';
    public string $collapse_label = '';
    public bool $has_icon = false;

    private static $iconMap = [
        'caret' => 'arrow-caret-down',
        'chevron' => 'arrow-chevron-down',
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
            ->required()
            ->allowedValues('caret', 'chevron');
        $resolver
            ->define('is_expanded')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('expand_label')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('collapse_label')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('has_icon')
            ->allowedTypes('bool')
            ->default(true);

        return $resolver->resolve($props) + $props;
    }

    #[PostMount]
    public function setIconName(): void
    {
        $this->icon_name = self::$iconMap[$this->type];
    }
}
