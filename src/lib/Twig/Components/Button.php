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

#[AsTwigComponent('ibexa:button')]
final class Button
{
    public string $size = 'medium';

    public string $type = 'primary';

    #[ExposeInTemplate('html_type')]
    public string $htmlType = 'button';

    public bool $disabled = false;

    public string $icon = '';

    /**
     * @var array{small: string, medium: string}
     */
    private static array $iconSizeMap = [
        'small' => 'tiny-small',
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
        if (isset($props['html_type']) && !isset($props['htmlType'])) {
            $props['htmlType'] = $props['html_type'];
            unset($props['html_type']);
        }

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

        return $resolver->resolve($props) + $props;
    }

    #[ExposeInTemplate('icon_size')]
    public function iconSize(): string
    {
        return self::$iconSizeMap[$this->size];
    }
}
