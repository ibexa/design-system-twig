<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\DesignSystemTwig\Templating\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CssClassExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ids_create_css_class',
                $this->createCssClass(...),
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    public function createCssClass(array $classes): string
    {
        $classList = array_keys(array_filter($classes));
        $classList = array_filter($classList, static fn ($value) => $value !== '');
        $classList = array_map('trim', $classList);

        return implode(' ', $classList);
    }
}
