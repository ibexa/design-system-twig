<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\InputText;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Twig\Markup;

#[AsTwigComponent('ibexa:input_text:input')]
final class Input
{
    public string $type = 'text';

    public string $size = 'medium';

    public string|Markup|null $actions_after = null;

    public bool $has_search_action = false;

    public string $search_button_type = 'submit';

    public bool $disabled = false;

    public bool $error = false;

    public bool $required = false;

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
            ->define('type')
            ->allowedValues('text', 'password', 'email', 'number', 'tel', 'search', 'url')
            ->default('text');
        $resolver
            ->define('size')
            ->allowedValues('small', 'medium')
            ->default('medium');
        $resolver
            ->define('actions_after')
            ->allowedTypes('null', 'string', Markup::class)
            ->default(null);
        $resolver
            ->define('has_search_action')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('search_button_type')
            ->allowedValues('button', 'reset', 'submit')
            ->default('submit');
        $resolver
            ->define('disabled')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('error')
            ->allowedTypes('bool')
            ->default(false);
        $resolver
            ->define('required')
            ->allowedTypes('bool')
            ->default(false);

        return $resolver->resolve($props) + $props;
    }
}
