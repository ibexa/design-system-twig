<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\formControls;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
final class InputText
{
    public string $id;

    public string $name;

    #[ExposeInTemplate(name: 'label_extra', getter: 'getLabelExtra')]
    public array $labelExtra = [];

    #[ExposeInTemplate('helper_text_extra')]
    public array $helperTextExtra = [];

    #[ExposeInTemplate(name: 'input', getter: 'getInput')]
    public array $input = [];

    public string $value = '';

    public string $type = 'input-text';

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
            ->define('labelExtra')
            ->allowedTypes('array')
            ->default([]);
        $resolver
            ->define('helperTextExtra')
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

    public function getLabelExtra(): array
    {
        return $this->labelExtra + ['for' => $this->id, 'required' => $this->input['required']];
    }

    public function getInput(): array
    {
        return $this->input + [
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'data-ids-custom-init' => 'true',
        ];
    }
}