<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\ToggleButton;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractSingleInputField;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Uuid;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ibexa:toggle_button:field')]
final class Field extends AbstractSingleInputField
{
    public string $type = 'toggle';

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $this->configureSingleInputFieldOptions(
            $resolver,
            static fn (): string => (string) Uuid::v7()
        );
        $resolver->setRequired(['name']);
    }
}
