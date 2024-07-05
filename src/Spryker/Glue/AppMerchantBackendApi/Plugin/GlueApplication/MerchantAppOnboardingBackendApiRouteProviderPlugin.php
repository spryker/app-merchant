<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Plugin\GlueApplication;

use Spryker\Glue\AppMerchantBackendApi\Controller\MerchantAppOnboardingResourceController;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RouteProviderPluginInterface;
use Spryker\Glue\Kernel\Backend\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class MerchantAppOnboardingBackendApiRouteProviderPlugin extends AbstractPlugin implements RouteProviderPluginInterface
{
    /**
     * @var string
     */
    public const ROUTE_URL_PATH = '/private/merchants/onboarding';

    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $routeCollection->add('postMerchantAppOnboarding', $this->getPostMerchantAppOnboardingRoute());

        return $routeCollection;
    }

    public function getPostMerchantAppOnboardingRoute(): Route
    {
        return (new Route(static::ROUTE_URL_PATH))->setDefaults([
                '_controller' => [MerchantAppOnboardingResourceController::class, 'postAction'],
                '_resourceName' => 'MerchantAppOnboarding',
            ])
            ->setMethods(Request::METHOD_POST);
    }
}
