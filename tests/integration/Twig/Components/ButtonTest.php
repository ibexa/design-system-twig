<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig\Twig\Components;

use Ibexa\DesignSystemTwig\Twig\Components\Button;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ButtonTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testMount(): void
    {
        $component = $this->mountTwigComponent(
            'ibexa:button',
            [
                'size' => 'small',
                'type' => 'secondary',
                'htmlType' => 'submit',
                'disabled' => true,
                'icon' => 'arrow-right',
            ]
        );

        self::assertInstanceOf(Button::class, $component);
        self::assertSame('small', $component->size, 'Size should be resolved to "small"');
        self::assertSame('secondary', $component->type, 'Type should be resolved to "secondary"');
        self::assertSame('submit', $component->htmlType, 'HTML type should be resolved to "submit"');
        self::assertTrue($component->disabled, 'Disabled should be true');
        self::assertSame('arrow-right', $component->icon, 'Icon name should be passed through');
        self::assertSame('tiny-small', $component->iconSize(), 'iconSize() should map "small" to "tiny-small"');
    }

    public function testDefaultRender(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', []);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);

        self::assertSame(1, $button->count(), 'There should be exactly one button');
        self::assertSame('button', $button->attr('type'), 'Button should have type="button" by default');

        $classAttr = (string) $button->attr('class');
        self::assertStringContainsString('ids-btn', $classAttr, 'Base class is missing');
        self::assertStringContainsString('ids-btn--primary', $classAttr, 'Default type should be "primary"');
        self::assertStringContainsString('ids-btn--medium', $classAttr, 'Default size should be "medium"');
        self::assertStringNotContainsString('ids-btn--disabled', $classAttr, 'Disabled class should not be present by default');

        self::assertSame(1, $button->filter('.ids-btn__label')->count(), 'Label container should be present');

        self::assertSame(0, $button->filter('.ids-btn__icon')->count(), 'Icon container should not be present without icon');
    }

    public function testDisabledStateRender(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'disabled' => true,
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);
        $classAttr = (string) $button->attr('class');

        self::assertStringNotContainsString('ids-btn--disabled', $classAttr, 'Disabled class should NOT be present');
        self::assertNotNull($button->attr('disabled'), 'Disabled attribute should be present');
    }

    public function testHtmlTypeRender(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'htmlType' => 'submit',
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);

        self::assertSame('submit', $button->attr('type'), 'Button should render custom HTML type');
    }

    public function testVariantAndSizeClasses(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'type' => 'secondary-alt',
            'size' => 'small',
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);
        $classAttr = (string) $button->attr('class');

        self::assertStringContainsString('ids-btn--secondary-alt', $classAttr, 'Type variant class should be applied');
        self::assertStringContainsString('ids-btn--small', $classAttr, 'Size class should be applied');
    }

    public function testMergesCustomClassesFromAttributes(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'attributes' => [
                'class' => 'u-ml-2 custom-hook',
                'data-test' => 'button-x',
            ],
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);
        $classAttr = (string) $button->attr('class');

        self::assertStringContainsString('ids-btn', $classAttr);
        self::assertStringContainsString('u-ml-2', $classAttr, 'Custom class should be merged into class attribute');
        self::assertStringContainsString('custom-hook', $classAttr, 'Custom class should be merged into class attribute');
        self::assertSame('button', $button->attr('type'), 'Default type attribute remains "button"');
        self::assertSame('button-x', $button->attr('data-test'), 'Arbitrary attributes should be preserved');
    }

    public function testIconRenderingIsConditional(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'icon' => 'arrow-right',
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);

        self::assertSame(1, $button->filter('.ids-btn__icon')->count(), 'Icon container should be present when icon is set');
    }

    public function testIconUrlRenderingIsConditional(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'icon_url' => '/assets/icons.svg#calendar-schedule',
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);

        self::assertSame(1, $button->filter('.ids-btn__icon')->count(), 'Icon container should be present when icon_url is set');
        self::assertSame('/assets/icons.svg#calendar-schedule', $button->filter('.ids-btn__icon use')->attr('xlink:href'));
    }

    public function testIconAndIconUrlAreMutuallyExclusive(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage("Options 'icon' and 'icon_url' cannot be used together.");

        $this->mountTwigComponent('ibexa:button', [
            'icon' => 'arrow-right',
            'icon_url' => '/assets/icons.svg#calendar-schedule',
        ]);
    }

    public function testResetHtmlTypeRender(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'htmlType' => 'reset',
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);

        self::assertSame('reset', $button->attr('type'), 'Button should render reset HTML type');
    }

    public function testIconPositionStart(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'icon' => 'arrow-right',
            'icon_position' => 'start',
            'label' => 'Click me',
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);
        $children = $button->children();

        self::assertSame(2, $children->count(), 'Button should have icon and label');

        $firstChildClass = $children->first()->attr('class');
        self::assertNotNull($firstChildClass);
        self::assertStringContainsString('ids-btn__icon', $firstChildClass, 'Icon should be first child when position is "start"');

        $secondChildClass = $children->eq(1)->attr('class');
        self::assertNotNull($secondChildClass);
        self::assertStringContainsString('ids-btn__label', $secondChildClass, 'Label should be second child when position is "start"');
    }

    public function testIconPositionEnd(): void
    {
        $rendered = $this->renderTwigComponent('ibexa:button', [
            'icon' => 'caret-next',
            'icon_position' => 'end',
            'label' => 'Click me',
        ]);
        $crawler = $rendered->crawler();

        $button = $this->getButton($crawler);
        $children = $button->children();

        self::assertSame(2, $children->count(), 'Button should have label and icon');

        $firstChildClass = $children->first()->attr('class');
        self::assertNotNull($firstChildClass);
        self::assertStringContainsString('ids-btn__label', $firstChildClass, 'Label should be first child when position is "end"');

        $secondChildClass = $children->eq(1)->attr('class');
        self::assertNotNull($secondChildClass);
        self::assertStringContainsString('ids-btn__icon', $secondChildClass, 'Icon should be second child when position is "end"');
    }

    private function getButton(Crawler $crawler): Crawler
    {
        return $crawler->filter('button.ids-btn');
    }
}
