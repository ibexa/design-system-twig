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

#[AsTwigComponent]
final class Badge
{
    private const DEFAULT_MAX_BADGE_VALUE = 99;
    public string $size = 'medium';

    public int $value = 1;

    #[ExposeInTemplate('max_value')]
    public int $maxBadgeValue;

    public function __construct()
    {
        $this->maxBadgeValue = self::DEFAULT_MAX_BADGE_VALUE;
    }

    /**
     * @param array<string, mixed> $props
     *
     * @return array<string, mixed>
     */
    #[PreMount]
    public function validate(array $props): array
    {
        $resolver = new OptionsResolver();
        $resolver->setIgnoreUndefined(true);
        $resolver
            ->define('size')
            ->allowedValues('small', 'medium')
            ->default('medium');
        $resolver
            ->define('value')
            ->allowedTypes('int')
            ->required();
        $resolver
            ->define('maxBadgeValue')
            ->allowedTypes('int')
            ->default(self::DEFAULT_MAX_BADGE_VALUE);

        return $resolver->resolve($props);
    }

    #[ExposeInTemplate('is_wide')]
    public function isWide(): bool
    {
        return $this->value > $this->maxBadgeValue;
    }

    #[ExposeInTemplate('formatted_value')]
    public function getFormattedValue(): string
    {
        if ($this->value > $this->maxBadgeValue) {
            return $this->maxBadgeValue . '+';
        }

        return (string)$this->value;
    }
}
