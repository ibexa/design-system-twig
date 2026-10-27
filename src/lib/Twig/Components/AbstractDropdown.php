<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;
use Twig\Markup;
use Twig\Runtime\EscaperRuntime;

/**
 * @phpstan-type TDropdownItem array{
 *     id: string,
 *     label: string
 * }
 * @phpstan-type TDropdownItemGroup array{
 *     label: string,
 *     items: array<int, array<string, mixed>>,
 *     id?: string
 * }
 * @phpstan-type TDropdownEntry TDropdownItem|TDropdownItemGroup
 */
abstract class AbstractDropdown
{
    public string $name;

    public ?string $source = null;

    /** @var array<string, mixed> */
    public array $sourceAttributes = [];

    public bool $disabled = false;

    public bool $error = false;

    /** @var array<TDropdownEntry> */
    public array $items = [];

    /** @var array<string> */
    public array $itemTemplateProps = ['id', 'label'];

    public string $placeholder;

    #[ExposeInTemplate('max_visible_items')]
    public int $maxVisibleItems = 10;

    private ?string $groupIdPrefix = null;

    /** @var array<int, TDropdownItem>|null */
    private ?array $flatItems = null;

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly Environment $twig,
    ) {}

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
            ->define('source')
            ->allowedTypes('null', 'string', Markup::class)
            ->normalize(static fn (Options $options, string | Markup | null $source): ?string => $source !== null ? (string) $source : null)
            ->default(null);
        $resolver
            ->define('sourceAttributes')
            ->allowedTypes('array')
            ->default([]);
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
        return count($this->getFlatItems()) > $this->maxVisibleItems;
    }

    /**
     * @return array<int, TDropdownItem>
     */
    #[ExposeInTemplate('flat_items')]
    public function getFlatItems(): array
    {
        return $this->flatItems ??= self::flattenEntries($this->items);
    }

    /**
     * @param array<string> $ids
     *
     * @return list<TDropdownItem>
     */
    protected function filterItemsByIds(array $ids): array
    {
        return array_values(array_filter(
            $this->getFlatItems(),
            static fn (array $item): bool => in_array($item['id'], $ids, true)
        ));
    }

    /**
     * @return array<int, TDropdownEntry>
     */
    #[ExposeInTemplate('source_entries')]
    public function getSourceEntries(): array
    {
        $sourceEntries = [];

        foreach ($this->items as $entry) {
            if (self::isItemGroup($entry)) {
                $entry['items'] = self::flattenEntries($entry['items']);
            }

            $sourceEntries[] = $entry;
        }

        return $sourceEntries;
    }

    #[ExposeInTemplate('group_id_prefix')]
    public function getGroupIdPrefix(): string
    {
        return $this->groupIdPrefix ??= sprintf('ids-dropdown-%s', bin2hex(random_bytes(4)));
    }

    /**
     * @param array<int, array<string, mixed>> $entries
     *
     * @return array<int, TDropdownItem>
     */
    private static function flattenEntries(array $entries): array
    {
        $flatItems = [];

        foreach ($entries as $entry) {
            if (self::isItemGroup($entry)) {
                array_push($flatItems, ...self::flattenEntries($entry['items']));

                continue;
            }

            $flatItems[] = $entry;
        }

        return $flatItems;
    }

    /**
     * @param array<string, mixed> $entry
     *
     * @phpstan-assert-if-true TDropdownItemGroup $entry
     *
     * @phpstan-assert-if-false TDropdownItem $entry
     */
    private static function isItemGroup(array $entry): bool
    {
        return array_key_exists('items', $entry);
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

    #[ExposeInTemplate('source_attributes')]
    public function getSourceAttributes(): ComponentAttributes
    {
        return new ComponentAttributes($this->sourceAttributes, $this->twig->getRuntime(EscaperRuntime::class));
    }

    abstract protected function configurePropsResolver(OptionsResolver $resolver): void;

    /**
     * @param Options<array<string, mixed>> $options
     * @param array<int, mixed> $items
     *
     * @return array<int, TDropdownEntry>
     */
    private static function normalizeItems(
        Options $options,
        array $items
    ): array {
        $normalizedEntries = self::normalizeEntries($items, '', self::createItemResolver(), self::createGroupResolver());

        self::assertUniqueIds('item', array_column(self::flattenEntries($normalizedEntries), 'id'));
        self::assertUniqueIds('group', self::collectGroupIds($normalizedEntries));

        return $normalizedEntries;
    }

    /**
     * @param array<int, string> $ids
     */
    private static function assertUniqueIds(
        string $kind,
        array $ids
    ): void {
        $duplicatedIds = array_unique(array_diff_assoc($ids, array_unique($ids)));

        if ($duplicatedIds !== []) {
            throw new InvalidOptionsException(
                sprintf('Dropdown %s ids must be unique, "%s" given more than once.', $kind, implode('", "', $duplicatedIds))
            );
        }
    }

    /**
     * @param array<int, array<string, mixed>> $entries
     *
     * @return array<int, string>
     */
    private static function collectGroupIds(array $entries): array
    {
        $groupIds = [];

        foreach ($entries as $entry) {
            if (!self::isItemGroup($entry)) {
                continue;
            }

            if (isset($entry['id'])) {
                $groupIds[] = $entry['id'];
            }

            array_push($groupIds, ...self::collectGroupIds($entry['items']));
        }

        return $groupIds;
    }

    /**
     * @param array<int, mixed> $entries
     *
     * @return array<int, TDropdownEntry>
     */
    private static function normalizeEntries(
        array $entries,
        string $path,
        OptionsResolver $itemResolver,
        OptionsResolver $groupResolver
    ): array {
        $normalizedEntries = [];

        foreach ($entries as $index => $entry) {
            $entryPath = $path === '' ? (string) $index : sprintf('%s.%s', $path, $index);

            if (!is_array($entry)) {
                throw new InvalidOptionsException(
                    sprintf(
                        'Each dropdown item must be an array, "%s" given at index %s.',
                        get_debug_type($entry),
                        $entryPath
                    )
                );
            }

            if (self::isItemGroup($entry)) {
                $group = self::normalizeItemGroup($entry, $entryPath, $itemResolver, $groupResolver);

                if ($group !== null) {
                    $normalizedEntries[] = $group;
                }

                continue;
            }

            /** @var TDropdownItem $resolvedItem */
            $resolvedItem = self::resolveEntry($itemResolver, $entry, $entryPath);

            $normalizedEntries[] = $resolvedItem;
        }

        return $normalizedEntries;
    }

    /**
     * @param array<string, mixed> $entry
     *
     * @return array<string, mixed>
     */
    private static function resolveEntry(
        OptionsResolver $resolver,
        array $entry,
        string $path
    ): array {
        try {
            return $resolver->resolve($entry);
        } catch (ExceptionInterface $exception) {
            throw new InvalidOptionsException(
                sprintf('Invalid dropdown entry at index %s: %s', $path, $exception->getMessage()),
                0,
                $exception
            );
        }
    }

    /**
     * @param array<string, mixed> $group
     *
     * @return TDropdownItemGroup|null
     */
    private static function normalizeItemGroup(
        array $group,
        string $path,
        OptionsResolver $itemResolver,
        OptionsResolver $groupResolver
    ): ?array {
        /** @var array{label: string, items: array<int, mixed>, id?: string} $resolvedGroup */
        $resolvedGroup = self::resolveEntry($groupResolver, $group, $path);
        $groupEntries = self::normalizeEntries($resolvedGroup['items'], $path, $itemResolver, $groupResolver);

        if ($groupEntries === []) {
            return null;
        }

        $resolvedGroup['items'] = $groupEntries;

        return $resolvedGroup;
    }

    private static function createGroupResolver(): OptionsResolver
    {
        $groupResolver = new OptionsResolver();
        $groupResolver
            ->setRequired(['label', 'items'])
            ->setDefined(['id'])
            ->setAllowedTypes('label', 'string')
            ->setAllowedTypes('items', 'array')
            ->setAllowedTypes('id', ['int', 'string'])
            ->setNormalizer('id', static fn (Options $groupOptions, int | string $id): string => (string) $id);

        return $groupResolver;
    }

    private static function createItemResolver(): OptionsResolver
    {
        $itemResolver = new OptionsResolver();
        $itemResolver
            ->setRequired(['id', 'label'])
            ->setAllowedTypes('id', ['int', 'string'])
            ->setNormalizer('id', static fn (Options $itemOptions, int | string $id): string => (string) $id)
            ->setAllowedTypes('label', 'string');

        return $itemResolver;
    }
}
