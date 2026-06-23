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

#[AsTwigComponent('ibexa:accordion')]
final class Accordion
{
    #[ExposeInTemplate('initially_expanded')]
    public bool $initiallyExpanded = false;

    #[ExposeInTemplate('expander_type')]
    public string $expanderType = 'caret';

    #[ExposeInTemplate('expander_has_icon')]
    public bool $expanderHasIcon = true;

    #[ExposeInTemplate('expander_has_label')]
    public bool $expanderHasLabel = true;

    #[ExposeInTemplate('expander_expand_label')]
    public string $expanderExpandLabel = '';

    #[ExposeInTemplate('expander_collapse_label')]
    public string $expanderCollapseLabel = '';

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
            ->define('initiallyExpanded')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('expanderType')
            ->allowedValues('caret', 'chevron')
            ->default('caret');
        $resolver
            ->define('expanderHasIcon')
            ->allowedTypes('bool')
            ->default(true);
        $resolver
            ->define('expanderHasLabel')
            ->allowedTypes('bool')
            ->default(true);
        $resolver
            ->define('expanderExpandLabel')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('expanderCollapseLabel')
            ->allowedTypes('string')
            ->default('');

        return $resolver->resolve($props) + $props;
    }
}
