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

abstract class AbstractChoiceInput
{
    public string $name;

    public bool $checked = false;

    public bool $disabled = false;

    public bool $error = false;

    #[ExposeInTemplate('input_class')]
    public string $inputClass = '';

    public bool $required = false;

    public string $size = 'medium';

    public ?string $value = null;

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
            ->define('checked')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('disabled')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('error')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('inputClass')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('required')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('size')
            ->allowedValues('small', 'medium')
            ->default('medium');
        $resolver
            ->define('value')
            ->allowedTypes('null', 'string')
            ->default(null);

        $this->configurePropsResolver($resolver);

        return $resolver->resolve($props) + $props;
    }

    // #[ExposeInTemplate('value')]
    // public function getValue(): ?string
    // {
    //     dump(self::value);
    //     return $this->value;
    // }

    abstract protected function configurePropsResolver(OptionsResolver $resolver): void;

    abstract public function getType(): string;
}
