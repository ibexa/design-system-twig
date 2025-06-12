<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\DesignSystemTwig\Templating\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TemplateExtension extends AbstractExtension
{
    private $base_template_path = '@IbexaDesignSystemTwig/themes/standard/';

    public function __construct()
    {
    //     $this->iconPathResolver = $iconPathResolver;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_get_template',
                $this->getTemplate(...),
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    public function getTemplate(string $relative_template_path): string
    {
        return $this->base_template_path . $relative_template_path . '.html.twig';
    }
}
