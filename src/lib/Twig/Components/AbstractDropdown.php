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
 * @phpstan-type TDropdownItem array{
 *     id: string,
 *     label: string
 * }
 */
abstract class AbstractDropdown
{
    public string $name;

    public bool $disabled = false;

    public bool $error = false;

    /** @var array<TDropdownItem> */
    public array $items = [];

    /** @var array<string> */
    public array $itemTemplateProps = ['id', 'label'];

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
                    'ibexa_design_system_twig'
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

    /**
     * @return array<string, string>
     */
    #[ExposeInTemplate('item_template_props')]
    public function getItemTemplateProps(): array
    {
        $itemPropsPatterns = array_map(
            static fn (string $name): string => '{{ ' . $name . ' }}',
            $this->itemTemplateProps
        );

        return array_combine($this->itemTemplateProps, $itemPropsPatterns);
    }

    abstract protected function configurePropsResolver(OptionsResolver $resolver): void;

    /**
     * @param Options<array<string, mixed>> $options
     * @param array<int, mixed> $items
     *
     * @return array<int, TDropdownItem>
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
            if (!is_array($item)) {
                throw new InvalidOptionsException(
                    sprintf(
                        'Each dropdown item must be an array, "%s" given at index %d.',
                        get_debug_type($item),
                        $index
                    )
                );
            }

            $items[$index] = $itemResolver->resolve($item);
        }

        return $items;
    }
}
