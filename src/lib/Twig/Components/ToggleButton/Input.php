<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\ToggleButton;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractChoiceInput;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('ibexa:toggle_button:input')]
final class Input extends AbstractChoiceInput
{
    private const string TRANSLATION_DOMAIN = 'ibexa_design_system_twig';

    public string $offLabel = '';

    public string $onLabel = '';

    public string $id = '';

    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->define('offLabel')
            ->allowedTypes('string')
            ->default(
                $this->translator->trans(
                    /** @Desc("Off") */
                    'ids.toggle.label.off',
                    [],
                    self::TRANSLATION_DOMAIN
                )
            );
        $resolver
            ->define('onLabel')
            ->allowedTypes('string')
            ->default(
                $this->translator->trans(
                    /** @Desc("On") */
                    'ids.toggle.label.on',
                    [],
                    self::TRANSLATION_DOMAIN
                )
            );
        $resolver
            ->define('id')
            ->allowedTypes('string')
            ->default((string)Uuid::v7());
    }

    #[ExposeInTemplate('type')]
    public function getType(): string
    {
        return '';
    }
}
