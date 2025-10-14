<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components\InputText;

use Ibexa\DesignSystemTwig\Twig\Components\AbstractField;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * @phpstan-type AttrMap array<string, scalar>
 */
#[AsTwigComponent('ibexa:input_text:field')]
final class Field extends AbstractField
{
    /** @var non-empty-string */
    public string $id;

    /** @var AttrMap */
    #[ExposeInTemplate(name: 'input', getter: 'getInput')]
    public array $input = [];

    public string $type = 'input-text';

    public string $value = '';

    /**
     * @return AttrMap
     */
    public function getLabelExtra(): array
    {
        return $this->labelExtra + ['for' => $this->id, 'required' => $this->required];
    }

    /**
     * @return AttrMap
     */
    public function getInput(): array
    {
        return $this->input + [
            'id' => $this->id,
            'name' => $this->name,
            'required' => $this->required,
            'value' => $this->value,
            'data-ids-custom-init' => 'true',
        ];
    }

    protected function configurePropsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'id' => null,
            'input' => [],
        ]);
        $resolver->setAllowedTypes('input', 'array');
        $resolver->setNormalizer('input', static function (Options $options, array $attributes) {
            return self::assertForbidden($attributes, ['id', 'name', 'required', 'value'], 'input');
        });
        $resolver->setRequired(['name']);
        $resolver->setAllowedTypes('id', ['null', 'string']);
        $resolver->setDefaults(['value' => '']);
        $resolver->setAllowedTypes('value', 'string');
    }
}
