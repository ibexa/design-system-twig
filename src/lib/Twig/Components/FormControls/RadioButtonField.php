<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\FormControls;

use Ibexa\DesignSystemTwig\Twig\Components\Inputs\AbstractRadioButton;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ibexa:form_controls:radio_button_field')]
final class RadioButtonField extends AbstractRadioButton
{
    use LabelledChoiceInputTrait;

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        parent::configurePropsResolver($resolver);

        $this->validateLabelledProps($resolver);
    }
}
