<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

abstract class AbstractChoiceInputWithLabel
{
    public string $id;

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
        $resolver
            ->define('id')
            ->required()
            ->allowedTypes('string');

        $this->configurePropsResolver($resolver);

        return $resolver->resolve($props) + $props;
    }

    abstract protected function configurePropsResolver(OptionsResolver $resolver): void;
}
