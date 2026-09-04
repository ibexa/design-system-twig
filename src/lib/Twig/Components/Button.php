<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('ibexa:button')]
final class Button
{
    public string $size = 'medium';

    public string $type = 'primary';

    #[ExposeInTemplate('html_type')]
    public string $htmlType = 'button';

    public bool $disabled = false;

    public string $icon = '';

    public string $icon_url = '';

    public string $label = '';

    public string $icon_position = 'start';

    /**
     * @var array{small: string, medium: string}
     */
    private static array $iconSizeMap = [
        'small' => 'small',
        'medium' => 'small',
    ];

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
            ->define('size')
            ->allowedValues('small', 'medium')
            ->default('medium');
        $resolver
            ->define('type')
            ->allowedValues('primary', 'secondary', 'tertiary', 'secondary-alt', 'tertiary-alt')
            ->default('primary');
        $resolver
            ->define('htmlType')
            ->allowedValues('button', 'submit', 'reset')
            ->default('button');
        $resolver
            ->define('disabled')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('icon')
            ->allowedTypes('string');
        $resolver
            ->define('icon_url')
            ->allowedTypes('string');
        $resolver
            ->define('icon_position')
            ->allowedValues('start', 'end')
            ->default('start');

        $resolvedProps = $resolver->resolve($props) + $props;

        if (($resolvedProps['icon'] ?? '') !== '' && ($resolvedProps['icon_url'] ?? '') !== '') {
            throw new InvalidOptionsException("Options 'icon' and 'icon_url' cannot be used together.");
        }

        return $resolvedProps;
    }

    #[ExposeInTemplate('icon_size')]
    public function iconSize(): string
    {
        return self::$iconSizeMap[$this->size];
    }
}
