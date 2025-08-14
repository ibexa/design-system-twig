<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
final class IconButton extends AbstractButton
{
    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver->setRequired('icon');
    }

    /**
     * @param array<string, mixed> $props
     *
     * @return array<string, mixed>
     */
    #[PostMount]
    public function setExtraClasses(array $props): array
    {
        $props['class'] = ($props['class'] ?? '') . ' ids-btn--icon-only';

        return $props;
    }
}
