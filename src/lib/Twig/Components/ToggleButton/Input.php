<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\ToggleButton;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractChoiceInput;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent(name: 'ibexa:toggle_button:input', attributesVar: '_attributes')]
final class Input extends AbstractChoiceInput
{
    public string $offLabel = '';

    public string $onLabel = '';

    public string $id = '';

    /** @var array<string, string> */
    public array $attributes = [];

    public string $class = '';

    public bool $customInit = false;

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver->setIgnoreUndefined(false);
        $resolver
            ->define('offLabel')
            ->allowedTypes('string')
            ->default('Off'); // TODO: add translation after dropdown merge
        $resolver
            ->define('onLabel')
            ->allowedTypes('string')
            ->default('On');


        $resolver
            ->define('id')
            ->allowedTypes('string')
            ->default('');

            
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

    #[ExposeInTemplate('type')]
    public function getType(): string
    {
        return 'radio';
    }

    #[PostMount]
    public function postMount(): void
    {
        if ($this->customInit) {
            $this->attributes['data-ids-custom-init'] = '1';
        }
    }
}
