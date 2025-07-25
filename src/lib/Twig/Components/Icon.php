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
    name: 'ibexa:icon',
    template: '@ibexadesign/design_system/components/icon.html.twig'
)]
final class Icon extends AbstractIcon
{
    public string $name;

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver->define('name')->required()->allowedTypes('string');
    }
}
