<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use InvalidArgumentException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('ibexa:overflow_list')]
final class OverflowList
{
    /** @var list<array> */
    public array $items = [];

    /** @var list<string> */
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
            ->normalize(self::normalizeItems(...));
        $resolver
            ->define('itemTemplateProps')
            ->allowedTypes('array')
            ->default([])
            ->normalize(self::normalizeItemTemplateProps(...));

        return $resolver->resolve($props) + $props;
    }

    /**
     * @return array<string, string>
     */
    #[ExposeInTemplate('item_template_props')]
    public function getItemTemplateProps(): array
    {
        if (empty($this->itemTemplateProps)) {
            return [];
        }

        $props = [];
        foreach ($this->itemTemplateProps as $name) {
            $props[$name] = '{{ ' . $name . ' }}';
        }

        return $props;
    }

    /**
     * @param Options<array<string, mixed>> $options
     * @param array<int, mixed> $value
     *
     * @return list<array<string, mixed>>
     */
    private static function normalizeItems(Options $options, array $value): array
    {
        if (!array_is_list($value)) {
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
    }

    /**
     * @param Options<array<string, mixed>> $options
     * @param array<int|string, mixed> $value
     *
     * @return array<int, string>
     */
    private static function normalizeItemTemplateProps(Options $options, array $value): array
    {
        foreach ($value as $key => $prop) {
            if (!is_string($prop)) {
                $index = is_int($key) ? (string) $key : sprintf('"%s"', $key);
                throw new InvalidArgumentException(sprintf('itemTemplateProps[%s] must be a string, %s given.', $index, get_debug_type($prop)));
            }

            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $prop)) {
                throw new InvalidArgumentException(sprintf('Invalid itemTemplateProps value "%s".', $prop));
            }
        }

        return array_values($value);
    }
}
