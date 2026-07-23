<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig;

use Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle;
use Ibexa\Bundle\DesignSystemTwig\IbexaDesignSystemTwigBundle;
use Ibexa\Bundle\TwigComponents\IbexaTwigComponentsBundle;
use Ibexa\Contracts\Test\Core\IbexaTestKernel as BaseIbexaTestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

final class IbexaTestKernel extends BaseIbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield from [
            new WebpackEncoreBundle(),
            new TwigComponentBundle(),
            new TwigExtraBundle(),
            new IbexaTwigComponentsBundle(),
            new IbexaDesignEngineBundle(),
            new IbexaDesignSystemTwigBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/config.yaml');
    }
}
