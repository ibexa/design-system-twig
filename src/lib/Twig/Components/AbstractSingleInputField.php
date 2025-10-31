<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignSystemTwig\Twig\Components;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\InvalidTypeException;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * @phpstan-type AttributeMap array<string, scalar>
 */
abstract class AbstractSingleInputField extends AbstractField
{
    /** @var non-empty-string */
    public string $id;

    /** @var AttributeMap */
    #[ExposeInTemplate(name: 'input', getter: 'getInput')]
    public array $input = [];

    public string $value = '';

    /**
     * @return AttributeMap
     */
    public function getLabelExtra(): array
    {
        return $this->labelExtra + ['for' => $this->id, 'required' => $this->required];
    }

    /**
     * @return AttributeMap
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

    protected function configureSingleInputFieldOptions(
        OptionsResolver $resolver,
        ?callable $idFactory,
        string $defaultValue = ''
    ): void {
        $resolver
            ->define('id')
            ->allowedTypes('null', 'string')
            ->default(null)
            ->normalize(static function (Options $options, ?string $id) use ($idFactory): string {
                if (null !== $id) {
                    if ('' === trim($id)) {
                        throw new InvalidTypeException('non-empty-string', 'string', 'id');
                    }

                    return $id;
                }

                if (null === $idFactory) {
                    throw new InvalidTypeException('string', 'NULL', 'id');
                }

                $value = $idFactory();

                if ('' === trim($value)) {
                    throw new InvalidTypeException('non-empty-string', 'string', 'id');
                }

                return $value;
            });

        $resolver
            ->define('input')
            ->allowedTypes('array')
            ->default([])
            ->normalize(static function (Options $options, array $attributes): array {
                return self::assertForbidden($attributes, ['id', 'name', 'required', 'value'], 'input');
            });

        $resolver
            ->define('value')
            ->allowedTypes('string')
            ->default($defaultValue);
    }
}
