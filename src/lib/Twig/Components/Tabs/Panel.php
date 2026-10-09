<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\Tabs;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('ibexa:tabs:panel')]
final class Panel
{
    public string $id = '';

    #[ExposeInTemplate('labelled_by')]
    public string $labelledBy = '';

    #[ExposeInTemplate('is_selected')]
    public bool $isSelected = false;

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
            ->define('id')
            ->required()
            ->allowedTypes('string')
            ->allowedValues(static fn (string $value): bool => trim($value) !== '');
        $resolver
            ->define('labelledBy')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('isSelected')
            ->allowedTypes('bool')
            ->default(false);

        return $resolver->resolve($props) + $props;
    }
}
