<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\formControls;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
final class InputText
{
    public string $id;
    public string $name;
    public array $label_extra = [];
    public array $helper_text_extra = [];
    public array $input = [];
    public string $value = '';

    /**
     * @param array<string, mixed> $props
     */
    #[PreMount]
    public function validate(array $props): array
    {
        $resolver = new OptionsResolver();
        $resolver->setIgnoreUndefined(true);
        $resolver
            ->define('name')
            ->required()
            ->allowedTypes('string');
        $resolver
            ->define('id')
            ->required()
            ->allowedTypes('string');
        $resolver
            ->define('label_extra')
            ->allowedTypes('array')
            ->default([]);
        $resolver
            ->define('helper_text_extra')
            ->allowedTypes('array')
            ->default([]);
        $resolver
            ->setOptions('input', function (OptionsResolver $inputResolver): void {
                $inputResolver->setIgnoreUndefined(true);
                $inputResolver
                    ->define('required')
                    ->allowedTypes('bool')
                    ->default(false);
            });
        $resolver
            ->define('value')
            ->allowedTypes('string')
            ->default('');

        return array_replace_recursive($resolver->resolve($props), $props);
    }

    #[PostMount]
    public function setSharedProps(): void
    {
        $this->label_extra['for'] = $this->id;
        $this->label_extra['required'] = $this->input['required'];
        $this->input['id'] = $this->id;
        $this->input['name'] = $this->name;
        $this->input['value'] = $this->value;
    }
}
