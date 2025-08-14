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
final class Expander
{
    public string $type;

    public bool $is_expanded = false;

    public string $expand_label = '';

    public string $collapse_label = '';

    public bool $has_icon = false;

    /**
     * @var array{caret: string, chevron: string}
     */
    private static array $iconMap = [
        'caret' => 'arrow-caret-down',
        'chevron' => 'arrow-chevron-down',
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

    #[ExposeInTemplate('icon_name')]
    public function iconName(): string
    {
        return self::$iconMap[$this->type] ?? '';
    }
}
