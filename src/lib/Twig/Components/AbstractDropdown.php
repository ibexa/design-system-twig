<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

abstract class AbstractDropdown
{
    public string $name;

    public bool $disabled = false;

    public bool $error = false;

    public array $items = [];

    public string $placeholder;

    #[ExposeInTemplate('max_visible_items')]
    public int $maxVisibleItems = 10;

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
            ->define('name')
            ->required()
            ->allowedTypes('string');
        $resolver
            ->define('disabled')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('items')
            ->allowedTypes('array')
            ->default([]);
            // TODO: resolve array structure of objects with 'id' (string/number) and 'label' (string) keys
        $resolver
            ->define('placeholder')
            ->allowedTypes('string')
            ->default('Select an item'); // TODO: translation
        $resolver
            ->define('maxVisibleItems')
            ->allowedTypes('int')
            ->default(10);

        $this->configurePropsResolver($resolver);

        return $resolver->resolve($props) + $props;
    }

    #[ExposeInTemplate('is_search_visible')]
    public function getIsSearchVisible(): bool
    {
        return count($this->items) > $this->maxVisibleItems;
    }

    abstract protected function configurePropsResolver(OptionsResolver $resolver): void;
}
