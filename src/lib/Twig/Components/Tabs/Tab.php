<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\Tabs;

use Stringable;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('ibexa:tabs:tab')]
final class Tab
{
    public string|Stringable $label = '';

    #[ExposeInTemplate('is_selected')]
    public bool $isSelected = false;

    #[ExposeInTemplate('has_error')]
    public bool $hasError = false;

    #[ExposeInTemplate('is_disabled')]
    public bool $isDisabled = false;

    public string $href = '';

    #[ExposeInTemplate('panel_id')]
    public string $panelId = '';

    #[ExposeInTemplate('item_class')]
    public string $itemClass = '';

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
            ->define('label')
            ->allowedTypes('string', Stringable::class)
            ->default('');
        $resolver
            ->define('isSelected')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('hasError')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('isDisabled')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('href')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('panelId')
            ->allowedTypes('string')
            ->default('');
        $resolver
            ->define('itemClass')
            ->allowedTypes('string')
            ->default('');

        return $resolver->resolve($props) + $props;
    }

    #[ExposeInTemplate('tag')]
    public function getTag(): string
    {
        return $this->href !== '' ? 'a' : 'button';
    }
}
