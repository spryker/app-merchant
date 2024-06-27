<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant;

use InvalidArgumentException;
use Spryker\Zed\AppMerchant\Dependency\Facade\AppMerchantToAppKernelFacadeBridge;
use Spryker\Zed\AppMerchant\Dependency\Facade\AppMerchantToAppKernelFacadeInterface;
use Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\AppMerchant\AppMerchantConfig getConfig()
 */
class AppMerchantDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const PLUGIN_PLATFORM = 'APP_MERCHANT:PLUGIN_PLATFORM';

    /**
     * @var string
     */
    public const FACADE_APP_KERNEL = 'APP_MERCHANT:FACADE_APP_KERNEL';

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->providePlatformPlugin($container);
        $container = $this->provideAppKernelFacade($container);

        return $container;
    }

    protected function providePlatformPlugin(Container $container): Container
    {
        $container->set(static::PLUGIN_PLATFORM, function (Container $container): AppMerchantPlatformPluginInterface {
            return $this->getPlatformPlugin();
        });

        return $container;
    }

    protected function getPlatformPlugin(): AppMerchantPlatformPluginInterface
    {
        throw new InvalidArgumentException('You need to implement getPlatformPlugin() method in your AppMerchantDependencyProvider to provide the platform plugin.');
    }

    protected function provideAppKernelFacade(Container $container): Container
    {
        $container->set(static::FACADE_APP_KERNEL, static function (Container $container): AppMerchantToAppKernelFacadeInterface {
            return new AppMerchantToAppKernelFacadeBridge($container->getLocator()->appKernel()->facade());
        });

        return $container;
    }
}
