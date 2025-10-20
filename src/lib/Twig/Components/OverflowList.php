<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use InvalidArgumentException;
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
            ->default([])
            ->normalize(static function ($options, $value): array {
                if (!is_array($value) || !array_is_list($value)) {
                    throw new InvalidArgumentException('Property "items" must be a list (sequential array).');
                }

                foreach ($value as $i => $item) {
                    if (!is_array($item)) {
                        throw new InvalidArgumentException(sprintf('items[%d] must be an array, %s given.', $i, get_debug_type($item)));
                    }
                    foreach (array_keys($item) as $key) {
                        if (!is_string($key)) {
                            throw new InvalidArgumentException(sprintf('items[%d] must use string keys.', $i));
                        }
                        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                            throw new InvalidArgumentException(sprintf('Invalid key "%s" in items[%d].', $key, $i));
                        }
                    }
                }

                return $value;
            });
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
        if (empty($this->items)) {
            return [];
        }

        $allKeys = [];
        foreach ($this->items as $item) {
            $allKeys = array_unique([...$allKeys, ...array_keys($item)]);
        }

        $props = [];
        foreach ($allKeys as $name) {
            $props[$name] = '{{ ' . $name . ' }}';
        }

        return $props;
    }
}
