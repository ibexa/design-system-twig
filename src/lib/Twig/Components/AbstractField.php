<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

/**
 * @phpstan-type AttributeMap array<string, scalar>
 */
abstract class AbstractField
{
    /** @var non-empty-string */
    public string $name;

    /** @var AttributeMap */
    #[ExposeInTemplate(name: 'label_extra', getter: 'getLabelExtra')]
    public array $labelExtra = [];

    /** @var AttributeMap */
    #[ExposeInTemplate('helper_text_extra')]
    public array $helperTextExtra = [];

    public bool $required = false;

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

        $resolver->setDefaults([
            'labelExtra' => [],
            'helperTextExtra' => [],
            'required' => false,
        ]);

        $resolver->setRequired(['name']);

        $resolver->setAllowedTypes('name', 'string');
        $resolver->setAllowedTypes('labelExtra', 'array');
        $resolver->setAllowedTypes('helperTextExtra', 'array');
        $resolver->setAllowedTypes('required', 'bool');

        $resolver->setNormalizer('labelExtra', static function (Options $options, array $attributes) {
            return self::assertForbidden($attributes, ['for', 'required'], 'labelExtra');
        });

        $this->configurePropsResolver($resolver);

        return array_replace_recursive($resolver->resolve($props), $props);
    }

    /**
     * @return AttributeMap
     */
    public function getLabelExtra(): array
    {
        return $this->labelExtra + ['required' => $this->required];
    }

    /**
     * @param array<string, scalar> $attributes
     * @param list<string> $forbidden
     * @param non-empty-string $optionName
     *
     * @return array<string, scalar>
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    protected static function assertForbidden(array $attributes, array $forbidden, string $optionName): array
    {
        $disallowedKeys = array_intersect(array_keys($attributes), $forbidden);
        if ($disallowedKeys) {
            throw new InvalidOptionsException(sprintf(
                'Option "%s" cannot contain the following keys: %s.',
                $optionName,
                implode(', ', $disallowedKeys)
            ));
        }

        return $attributes;
    }

    abstract protected function configurePropsResolver(OptionsResolver $resolver): void;
}
