<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi;

use Spryker\Glue\AppMerchantBackendApi\Dependency\Facade\AppMerchantBackendApiToAppMerchantFacadeBridge;
use Spryker\Glue\AppMerchantBackendApi\Dependency\Facade\AppMerchantBackendApiToAppMerchantFacadeInterface;
use Spryker\Glue\Kernel\Backend\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Backend\Container;

/**
 * @method \Spryker\Glue\AppMerchantBackendApi\AppMerchantBackendApiConfig getConfig()
 */
class AppMerchantBackendApiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_APP_MERCHANT = 'FACADE_APP_MERCHANT';

    public function provideBackendDependencies(Container $container): Container
    {
        $container = parent::provideBackendDependencies($container);
        $container = $this->addAppMerchantFacade($container);

        return $container;
    }

    protected function addAppMerchantFacade(Container $container): Container
    {
        $container->set(static::FACADE_APP_MERCHANT, static function (Container $container): AppMerchantBackendApiToAppMerchantFacadeInterface {
            return new AppMerchantBackendApiToAppMerchantFacadeBridge($container->getLocator()->appMerchant()->facade());
        });

        return $container;
    }
}
