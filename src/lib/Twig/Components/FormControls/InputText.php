<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\FormControls;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

/**
 * @phpstan-type AttrMap array<string, scalar>
 */
#[AsTwigComponent('ibexa:form_controls:input_text')]
final class InputText
{
    /** @var non-empty-string */
    public string $id;

    /** @var non-empty-string */
    public string $name;

    /** @var AttrMap */
    #[ExposeInTemplate(name: 'label_extra', getter: 'getLabelExtra')]
    public array $labelExtra = [];

    /** @var AttrMap */
    #[ExposeInTemplate('helper_text_extra')]
    public array $helperTextExtra = [];

    /** @var AttrMap */
    #[ExposeInTemplate(name: 'input', getter: 'getInput')]
    public array $input = [];

    public bool $required = false;

    public string $value = '';

    public string $type = 'input-text';

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
            'id' => null,
            'labelExtra' => [],
            'helperTextExtra' => [],
            'input' => [],
            'required' => false,
            'value' => '',
        ]);

        $resolver->setRequired(['name', 'id']);

        $resolver->setAllowedTypes('name', 'string');
        $resolver->setAllowedTypes('id', ['null', 'string']);
        $resolver->setAllowedTypes('labelExtra', 'array');
        $resolver->setAllowedTypes('helperTextExtra', 'array');
        $resolver->setAllowedTypes('input', 'array');
        $resolver->setAllowedTypes('required', 'bool');
        $resolver->setAllowedTypes('value', 'string');

        $resolver->setNormalizer('labelExtra', static function (Options $options, array $attributes) {
            return self::assertForbidden($attributes, ['for', 'required'], 'labelExtra');
        });

        $resolver->setNormalizer('input', static function (Options $options, array $attributes) {
            return self::assertForbidden($attributes, ['id', 'name', 'required', 'value'], 'input');
        });

        return array_replace_recursive($resolver->resolve($props), $props);
    }

    /**
     * @return AttrMap
     */
    public function getLabelExtra(): array
    {
        return $this->labelExtra + ['for' => $this->id, 'required' => $this->required];
    }

    /**
     * @return AttrMap
     */
    public function getInput(): array
    {
        return $this->input + [
            'id' => $this->id,
            'name' => $this->name,
            'required' => $this->required,
            'value' => $this->value,
            'data-ids-custom-init' => 'true',
        ];
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
    private static function assertForbidden(array $attributes, array $forbidden, string $optionName): array
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
}
