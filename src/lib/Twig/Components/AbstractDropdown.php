<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

/**
 * @phpstan-type DropdownItem array{
 *     id: string,
 *     label: string
 * }
 */
abstract class AbstractDropdown
{
    private const TRANSLATION_DOMAIN = 'ibexa_design_system_twig';

    public string $name;

    public bool $disabled = false;

    public bool $error = false;

    /** @var array<DropdownItem> */
    public array $items = [];

    public string $placeholder;

    #[ExposeInTemplate('max_visible_items')]
    public int $maxVisibleItems = 10;

    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

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
            ->default([])
            ->normalize(self::normalizeItems(...));
        $resolver
            ->define('placeholder')
            ->allowedTypes('string')
            ->default(
                $this->translator->trans(
                    /** @Desc("Select an item") */
                    'ids.dropdown.placeholder',
                    [],
                    self::TRANSLATION_DOMAIN
                )
            );
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

    /**
     * @param Options<array<string, mixed>> $options
     * @param array<int, mixed> $items
     *
     * @return array<int, DropdownItem>
     */
    private static function normalizeItems(Options $options, array $items): array
    {
        $itemResolver = new OptionsResolver();
        $itemResolver
            ->setRequired(['id', 'label'])
            ->setAllowedTypes('id', ['int', 'string'])
            ->setNormalizer('id', static fn (Options $itemOptions, int|string $id): string => (string) $id)
            ->setAllowedTypes('label', 'string');

        foreach ($items as $index => $item) {
            if (!\is_array($item)) {
                throw new InvalidOptionsException(
                    sprintf(
                        'Each dropdown item must be an array, "%s" given at index %d.',
                        get_debug_type($item),
                        $index
                    )
                );
            }

            $resolvedItem = $itemResolver->resolve($item);

            $items[$index] = $resolvedItem;
        }

        return $items;
    }
}
