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

#[AsTwigComponent('ibexa:switcher')]
final class Switcher
{
    /** @var array<int, array<string, mixed>> */
    public array $items = [];

    public ?string $selectedValue = null;

    public string $size = 'large';

    public string $type = 'backoffice';

    public bool $overflow = false;

    public string $moreLabel = 'More';

    public string $name = '';

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
            ->define('selectedValue')
            ->allowedTypes('string', 'null')
            ->default(null);
        $resolver
            ->define('size')
            ->allowedValues('large', 'small')
            ->default('large');
        $resolver
            ->define('type')
            ->allowedValues('backoffice', 'builders')
            ->default('backoffice');
        $resolver
            ->define('overflow')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('moreLabel')
            ->allowedTypes('string')
            ->default('More');
        $resolver
            ->define('name')
            ->allowedTypes('string')
            ->default('');

        return $resolver->resolve($props) + $props;
    }

    /**
     * The item that owns the roving tab stop: the selected item when it is enabled, otherwise the
     * first enabled item. Mirrors the React component's `activeValue`.
     */
    #[ExposeInTemplate('active_value')]
    public function getActiveValue(): ?string
    {
        $enabledValues = [];

        foreach ($this->items as $item) {
            if (empty($item['disabled'])) {
                $enabledValues[] = (string) ($item['value'] ?? '');
            }
        }

        if ($this->selectedValue !== null && in_array($this->selectedValue, $enabledValues, true)) {
            return $this->selectedValue;
        }

        return $enabledValues[0] ?? null;
    }
}
