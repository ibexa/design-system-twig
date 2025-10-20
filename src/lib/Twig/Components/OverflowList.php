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

#[AsTwigComponent('ibexa:overflow_list')]
final class OverflowList
{
    /**
     * @var list<array>
     */
    public array $items = [];

    /**
     * @var array<string>
     */
    public array $itemTemplateProps = [];

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
            ->define('items')
            ->allowedTypes('array')
            ->default([]);
        $resolver
            ->define('itemTemplateProps')
            ->allowedTypes('array')
            ->default([]);

        return $resolver->resolve($props) + $props;
    }

    /**
     * @return array<string, string>
     */
    #[ExposeInTemplate('item_template_props')]
    public function getItemTemplateProps(): array
    {
        $itemPropsNames = array_keys($this->items[0]);

        $itemPropsPatterns = array_map(
            static fn (string $name): string => '{{ ' . $name . ' }}',
            $this->itemTemplateProps
        );

        $props = array_combine($itemPropsNames, $itemPropsPatterns);

        if (empty($props)) {
            return [];
        }

        return $props;
    }
}
