<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ibexa:custom_icon',
    template: '@ibexadesign/design_system/components/icon.html.twig'
)]
final class CustomIcon extends AbstractIcon
{
    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver->define('path')->required()->allowedTypes('string');
    }
}
