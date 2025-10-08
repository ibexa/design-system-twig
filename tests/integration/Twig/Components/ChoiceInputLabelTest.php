<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\ChoiceInputLabel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ChoiceInputLabelTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            ChoiceInputLabel::class,
            [
                'for' => 'agree',
                'content' => 'I agree to terms',
                'attributes' => ['class' => 'extra-class', 'data-custom' => 'custom'],
            ]
        );

        self::assertInstanceOf(ChoiceInputLabel::class, $component, 'Component should mount as ChoiceInputLabel.');
        self::assertSame('agree', $component->for, 'Prop "for" should be set.');
        self::assertSame('I agree to terms', $component->content, 'Prop "content" should be set.');
    }

    public function testDefaultRenderWithForAndContent(): void
    {
        $crawler = $this->renderTwigComponent(
            ChoiceInputLabel::class,
            ['for' => 'newsletter', 'content' => 'Subscribe me']
        )->crawler();

        $label = $this->getLabel($crawler);

        self::assertSame('newsletter', $label->attr('for'), 'Rendered label "for" should equal provided id.');
        self::assertSame('Subscribe me', $this->getText($label), 'Rendered label should contain provided content.');

        $class = $this->getClassAttr($label);
        self::assertStringContainsString('ids-choice-input-label', $class, 'Base class "ids-choice-input-label" should be present.');
    }

    public function testAttributesMergeClassAndData(): void
    {
        $crawler = $this->renderTwigComponent(
            ChoiceInputLabel::class,
            [
                'for' => 'agree',
                'content' => 'I agree',
                'attributes' => [
                    'class' => 'extra-class',
                    'data-custom' => 'custom',
                ],
            ]
        )->crawler();

        $label = $this->getLabel($crawler);
        $class = $this->getClassAttr($label);

        self::assertStringContainsString('extra-class', $class, 'Custom class should be merged into label class attribute.');
        self::assertSame('custom', $label->attr('data-custom'), 'Custom data attribute should be rendered on the label.');
    }

    public function testInvalidForTypeCausesResolverErrorOnMount(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->mountTwigComponent(ChoiceInputLabel::class, ['for' => 123, 'content' => 'x']);
    }

    private function getLabel(Crawler $crawler): Crawler
    {
        $node = $crawler->filter('label.ids-choice-input-label')->first();
        self::assertGreaterThan(0, $node->count(), 'Label wrapper "label.ids-choice-input-label" should be present.');

        return $node;
    }

    private function getClassAttr(Crawler $node): string
    {
        return (string) $node->attr('class');
    }

    private function getText(Crawler $node): string
    {
        return trim($node->text(''));
    }
}
