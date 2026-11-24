<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

abstract class AbstractDropdownInput extends AbstractDropdown
{
    public const string SIZE_MEDIUM = 'medium';

    public const string SIZE_SMALL = 'small';

    public bool $disabled = false;

    public bool $error = false;

    public string $placeholder;

    public string $size = self::SIZE_MEDIUM;

    public function __construct(
        private readonly TranslatorInterface $translator,
        Environment $twig,
    ) {
        parent::__construct($twig);
    }

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->define('disabled')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('error')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('placeholder')
            ->allowedTypes('string')
            ->default(
                $this->translator->trans(
                    /** @Desc("Select an item") */
                    'ids.dropdown.placeholder',
                    [],
                    'ibexa_design_system_twig'
                )
            );
        $resolver
            ->define('size')
            ->allowedValues(self::SIZE_MEDIUM, self::SIZE_SMALL)
            ->default(self::SIZE_MEDIUM);
    }
}
