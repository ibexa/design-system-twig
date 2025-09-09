<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\Inputs;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('ibexa:inputs:radio_button')]
class RadioButton extends AbstractChoiceInput
{
    public string $value;

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->define('value')
            ->required()
            ->allowedTypes('string');
    }

    #[ExposeInTemplate('type')]
    public function getType(): string
    {
        return 'radio';
    }
}
