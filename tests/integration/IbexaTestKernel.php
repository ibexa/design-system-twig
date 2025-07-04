<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\DesignSystemTwig;

use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle;
use Ibexa\Bundle\DesignSystemTwig\IbexaDesignSystemTwigBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\TwigComponents\IbexaTwigComponentsBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Test\Core\IbexaTestKernel as BaseIbexaTestKernel;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
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
            new HautelookTemplatedUriBundle(),
            new KnpMenuBundle(),
            new WebpackEncoreBundle(),
            new TwigComponentBundle(),
            new TwigExtraBundle(),
            new IbexaAdminUiBundle(),
            new IbexaContentFormsBundle(),
            new IbexaNotificationsBundle(),
            new IbexaUserBundle(),
            new IbexaSearchBundle(),
            new IbexaRestBundle(),
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
