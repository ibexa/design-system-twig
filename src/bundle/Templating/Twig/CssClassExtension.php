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
    // /** @var \Ibexa\Contracts\AdminUi\Resolver\IconPathResolverInterface */
    // private $iconPathResolver;

    public function __construct()
    {
    //     $this->iconPathResolver = $iconPathResolver;
    }

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
        $class_list = array_keys(array_filter($classes));

        return implode(' ', $class_list);
    }
}
