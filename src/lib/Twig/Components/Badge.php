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

#[AsTwigComponent('ibexa:badge')]
final class Badge
{
    private const DEFAULT_MAX_BADGE_VALUE = 99;
    private const THRESHOLD = [
        'medium' => 100,
        'small' => 10,
    ];

    private const STRING_THRESHOLD = [
        'medium' => 3,
        'small' => 2,
    ];

    public string $size = 'medium';

    public string $value = '1';

    public string $variant = 'string';

    #[ExposeInTemplate('max_value')]
    public int $maxValue;

    public function __construct()
    {
        $this->maxValue = self::DEFAULT_MAX_BADGE_VALUE;
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
            ->allowedTypes('string', 'int')
            ->required();
        $resolver
            ->define('variant')
            ->allowedValues('string', 'number')
            ->default('string');
        $resolver
            ->define('maxValue')
            ->allowedTypes('int')
            ->default(self::DEFAULT_MAX_BADGE_VALUE);

        return $resolver->resolve($props);
    }

    #[ExposeInTemplate('is_stretched')]
    public function isStretched(): bool
    {
        if ($this->variant === 'number') {
            $numericValue = (int)$this->value;

            return $numericValue >= self::THRESHOLD[$this->size];
        }

        return strlen($this->value) >= self::STRING_THRESHOLD[$this->size];
    }

    #[ExposeInTemplate('formatted_value')]
    public function getFormattedValue(): string
    {
        if ($this->variant === 'string') {
            return (string)$this->value;
        }

        $numericValue = (int)$this->value;

        if ($numericValue > $this->maxValue) {
            return $this->maxValue . '+';
        }

        return (string)$numericValue;
    }
}
