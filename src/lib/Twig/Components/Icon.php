<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('ibexa:icon')]
final class Icon
{
    public string $name = '';

    public string $size = 'medium';

    public string $path = '';

    /**
     * @param array<string, mixed> $props
     *
     * @return array<string, mixed>
     */
    #[PreMount]
    public function validate(array $props): array
    {
        $resolver = new OptionsResolver();
        $resolver->setIgnoreUndefined();
        $resolver->define('name')->allowedTypes('string')->default('');
        $resolver
            ->define('size')
            ->allowedValues('tiny', 'tiny-small', 'small', 'small-medium', 'medium', 'medium-large', 'large', 'extra-large', 'large-huge', 'huge')
            ->default('medium');
        $resolver->define('path')->allowedTypes('string')->default('');

        if (empty($props['name'])) {
            $resolver->setRequired(['path']);
        }

        if (empty($props['path'])) {
            $resolver->setRequired(['name']);
        }

        return $resolver->resolve($props) + $props;
    }

    #[ExposeInTemplate('path')]
    public function getPath(): string
    {
        if ($this->path !== '') {
            return $this->path;
        }

        // TODO: for backend, implement here icon path resolver
        return sprintf(
            '/bundles/ibexaadminuiassets/vendors/ids-assets/dist/img/all-icons.svg#%s',
            $this->name
        );
    }
}
