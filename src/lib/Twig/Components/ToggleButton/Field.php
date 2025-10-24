<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\ToggleButton;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractField;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * @phpstan-type AttrMap array<string, scalar>
 */
#[AsTwigComponent('ibexa:toggle_button:field')]
final class Field extends AbstractField
{
    /** @var non-empty-string */
    public string $id; // TODO: maybe move to AbstractField?

    /** @var AttrMap */
    #[ExposeInTemplate(name: 'input', getter: 'getInput')]
    public array $input = [];

    public string $type = 'toggle';

    public string $value = '';

    public string $class = '';

    public bool $customInit = false;

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

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'id' => null,
            'input' => [],
        ]);
        $resolver->setAllowedTypes('input', 'array');
        $resolver->setNormalizer('input', static function (Options $options, array $attributes) {
            return self::assertForbidden($attributes, ['id', 'name', 'required', 'value'], 'input');
        });
        $resolver->setRequired(['name']);
        $resolver->setAllowedTypes('id', ['null', 'string']);
        $resolver->setDefaults(['value' => '']);
        $resolver->setAllowedTypes('value', 'string');


        $resolver
            ->define('attributes')
            ->allowedTypes('array')
            ->default([]);
        $resolver
            ->define('class')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('customInit')
            ->allowedTypes('bool')
            ->default(false);
    }

    #[PostMount]
    public function postMount(): void
    {
        if ($this->customInit) {
            $this->attributes['data-ids-custom-init'] = '1';
        }
    }
}
