<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractInputDropdown extends AbstractDropdown
{
    public const string SIZE_MEDIUM = 'medium';

    public const string SIZE_SMALL = 'small';

    public string $size = self::SIZE_MEDIUM;

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->define('size')
            ->allowedValues(self::SIZE_MEDIUM, self::SIZE_SMALL)
            ->default(self::SIZE_MEDIUM);
    }
}
