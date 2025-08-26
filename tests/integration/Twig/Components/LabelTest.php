<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\Label;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class LabelTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent('ibexa:Label', [
            'error' => true,
            'required' => true,
        ]);

        self::assertInstanceOf(Label::class, $component, 'Component should be instance of Label');
        self::assertTrue($component->error, 'Property "error" should be true');
        self::assertTrue($component->required, 'Property "required" should be true');
    }

    public function testDefaultRender(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:Label', []);
        $crawler = $rendered->crawler();

        $label = $this->getLabel($crawler);
        self::assertSame(1, $label->count(), 'There should be exactly one label');
        $classAttr = (string) $label->attr('class');

        self::assertStringContainsString('ids-label', $classAttr, 'Base class "ids-label" should be present');
        self::assertStringNotContainsString('ids-label--error', $classAttr, 'Error modifier should not be present by default');
        self::assertStringNotContainsString('ids-label--required', $classAttr, 'Required modifier should not be present by default');
    }

    /**
     * @dataProvider variantProvider
     *
     * @param array<string, mixed> $props
     * @param list<string> $expectedPresent
     */
    public function testVariantClasses(array $props, array $expectedPresent): void
    {
        $rendered = $this->renderTwigComponent('ibexa:Label', $props);
        $label = $this->getLabel($rendered->crawler());
        $classStr = (string) $label->attr('class');

        self::assertStringContainsString('ids-label', $classStr, 'Base class should be present');
        foreach ($expectedPresent as $cls) {
            self::assertStringContainsString($cls, $classStr, sprintf('Expected class "%s" should be present', $cls));
        }
    }

    public function testMergesCustomClassesAndAttributes(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:Label', [
            'attributes' => [
                'class' => 'u-mb-1 custom-hook',
                'for' => 'field_id',
                'data-test' => 'label-x',
            ],
        ]);

        $label = $this->getLabel($rendered->crawler());
        $classStr = (string) $label->attr('class');

        self::assertStringContainsString('ids-label', $classStr, 'Base class should be present');
        self::assertStringContainsString('u-mb-1', $classStr, 'Custom class "u-mb-1" should be merged');
        self::assertStringContainsString('custom-hook', $classStr, 'Custom class "custom-hook" should be merged');
        self::assertSame('field_id', $label->attr('for'), 'Attribute "for" should be preserved');
        self::assertSame('label-x', $label->attr('data-test'), 'Custom data attribute should be preserved');
    }

    /**
     * @return \Generator<string, array{0: array<string,mixed>, 1: list<string>}>
     */
    public static function variantProvider(): \Generator
    {
        yield 'error only' => [
            ['error' => true],
            ['ids-label--error'],
        ];

        yield 'required only' => [
            ['required' => true],
            ['ids-label--required'],
        ];

        yield 'error + required' => [
            ['error' => true, 'required' => true],
            ['ids-label--error', 'ids-label--required'],
        ];
    }

    private function getLabel(Crawler $crawler): Crawler
    {
        return $crawler->filter('label.ids-label');
    }
}
