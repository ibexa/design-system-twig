<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\formControls;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

trait LabelledChoiceInputTrait
{
    public string $id;

    #[ExposeInTemplate('input_wrapper_class')]
    public string $inputWrapperClassName;

    #[ExposeInTemplate('label_class')]
    public string $labelClassName;

    protected function validateLabelledProps(OptionsResolver $resolver): void
    {
        $resolver
            ->define('id')
            ->required()
            ->allowedTypes('string');
        $resolver
            ->define('inputWrapperClassName')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('labelClassName')
            ->allowedTypes('string')
            ->default('');
    }

    #[ExposeInTemplate('input')]
    public function getInput(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'disabled' => $this->disabled,
            'error' => $this->error,
            'required' => $this->required,
            'value' => $this->value,
        ];
    }
}
