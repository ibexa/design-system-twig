<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\inputs;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

abstract class AbstractChoiceInput
{
    public string $name;

    public bool $disabled = false;

    public bool $error = false;

    #[ExposeInTemplate('input_class')]
    public string $inputClass = '';

    public bool $required = false;

    public string $size = 'medium';

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

        $this->configurePropsResolver($resolver);

        return $resolver->resolve($props) + $props;
    }

    abstract protected function configurePropsResolver(OptionsResolver $resolver): void;

    abstract public function getType(): string;
}
