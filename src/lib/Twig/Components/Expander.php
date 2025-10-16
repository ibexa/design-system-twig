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

#[AsTwigComponent('ibexa:expander')]
final class Expander
{
    public string $type;

    #[ExposeInTemplate('is_expanded')]
    public bool $isExpanded = false;

    #[ExposeInTemplate('expand_label')]
    public string $expandLabel = '';

    #[ExposeInTemplate('collapse_label')]
    public string $collapseLabel = '';

    #[ExposeInTemplate('has_icon')]
    public bool $hasIcon = true;

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
            ->define('isExpanded')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('expandLabel')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('collapseLabel')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('hasIcon')
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
