<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\inputs;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
final class InputText
{
    public string $id;
    public string $name;
    public string $type = 'text';
    public string $size = 'medium';
    public bool $disabled = false;
    public bool $error = false;
    public bool $required = false;

    /**
     * @param array<string, mixed> $props
     */
    #[PreMount]
    public function validate(array $props): array
    {
        $resolver = new OptionsResolver();
        $resolver->setIgnoreUndefined(true);
        $resolver
            ->define('id')
            ->allowedTypes('string');
        $resolver
            ->define('name')
            ->allowedTypes('string');
        $resolver
            ->define('type')
            ->allowedValues('text', 'password', 'email', 'number', 'tel', 'search', 'url')
            ->default('text');
        $resolver
            ->define('size')
            ->allowedValues('small', 'medium')
            ->default('medium');
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

        if (!isset($props['name'])) {
            $resolver->setRequired('id');
        }

        if (!isset($props['id'])) {
            $resolver->setRequired('name');
        }

        if (isset($props['id']) || isset($props['name'])) {
            $resolver->setDefaults([
                'id' => '',
                'name' => '',
            ]);
        }

        return $resolver->resolve($props) + $props;
    }

    /**
     * @param array<string, mixed> $data
     */
    #[PostMount]
    public function setInputAttrs(array $data): array
    {
        if (!empty($this->id)) {
            $data['id'] = $this->id;
        }

        if (!empty($this->name)) {
            $data['name'] = $this->name;
        }

        $data['type'] = $this->type;
        $data['disabled'] = $this->disabled;
        $data['required'] = $this->required;

        return $data;
    }
}
