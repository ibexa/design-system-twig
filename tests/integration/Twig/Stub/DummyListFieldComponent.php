<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Stub;

use Ibexa\DesignSystemTwig\Twig\Components\ListFieldTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DummyListFieldComponent
{
    use ListFieldTrait;

    public string $name = 'group';

    public bool $required = false;

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    public function resolve(array $options): array
    {
        $resolver = new OptionsResolver();
        $this->validateListFieldProps($resolver);

        return $resolver->resolve($options);
    }
}
