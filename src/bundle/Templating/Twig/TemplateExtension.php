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
    private $baseTemplatePath = '@IbexaDesignSystemTwig/themes/standard/design_system/';
    private $defaultTemplatePath = 'components/';
    private $templatePathMapping = [
        'macros/html' => '',
    ];

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ids_get',
                $this->getTemplate(...),
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    public function getTemplate(string $templateName): string
    {
        $fullTemplatePath = $this->baseTemplatePath;

        if (isset($this->templatePathMapping[$templateName])) {
            $fullTemplatePath .= $this->templatePathMapping[$templateName];
        } else {
            $fullTemplatePath .= $this->defaultTemplatePath;
        }

        $fullTemplatePath .= $templateName . '.html.twig';

        return $fullTemplatePath;
    }
}
