<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\Checkbox;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractChoiceInput;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ibexa:checkbox:input')]
final class Input extends AbstractChoiceInput
{
    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
    }
}
