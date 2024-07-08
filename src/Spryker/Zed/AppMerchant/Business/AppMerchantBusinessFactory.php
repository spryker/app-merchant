<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business;

use Spryker\Zed\AppMerchant\AppMerchantDependencyProvider;
use Spryker\Zed\AppMerchant\Business\AppConfig\AppConfigLoader;
use Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding\MerchantAppOnboarding;
use Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding\Writer\MerchantAppOnboardingCreator;
use Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding\Writer\MerchantAppOnboardingCreatorInterface;
use Spryker\Zed\AppMerchant\Business\Message\MessageSender;
use Spryker\Zed\AppMerchant\Business\PaymentTransmissions\PaymentTransmissionsRequestExtender;
use Spryker\Zed\AppMerchant\Business\Webhook\WebhookHandler;
use Spryker\Zed\AppMerchant\Business\Webhook\WebhookRequestExtender;
use Spryker\Zed\AppMerchant\Dependency\Facade\AppMerchantToAppKernelFacadeInterface;
use Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\MessageBroker\Business\MessageBrokerFacade;
use Spryker\Zed\MessageBroker\Business\MessageBrokerFacadeInterface;

/**
 * @method \Spryker\Zed\AppMerchant\Persistence\AppMerchantEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\AppMerchant\Persistence\AppMerchantRepositoryInterface getRepository()
 * @method \Spryker\Zed\AppMerchant\AppMerchantConfig getConfig()
 */
class AppMerchantBusinessFactory extends AbstractBusinessFactory
{
    public function createMerchantAppOnboarding(): MerchantAppOnboarding
    {
        return new MerchantAppOnboarding(
            $this->getConfig(),
            $this->getRepository(),
            $this->getEntityManager(),
            $this->getPlatformProviderPlugin(),
            $this->createMessageSender(),
        );
    }

    public function createMessageSender(): MessageSender
    {
        return new MessageSender($this->getMessageBrokerFacade(), $this->getConfig());
    }

    public function getPlatformProviderPlugin(): AppMerchantPlatformPluginInterface
    {
        /** @phpstan-var \Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface */
        return $this->getProvidedDependency(AppMerchantDependencyProvider::PLUGIN_PLATFORM);
    }

    public function getMessageBrokerFacade(): MessageBrokerFacadeInterface
    {
        return new MessageBrokerFacade();
    }

    public function createMerchantAppOnboardingCreator(): MerchantAppOnboardingCreatorInterface
    {
        return new MerchantAppOnboardingCreator(
            $this->getPlatformProviderPlugin(),
            $this->getEntityManager(),
            $this->createAppConfigLoader(),
        );
    }

    public function createAppConfigLoader(): AppConfigLoader
    {
        return new AppConfigLoader($this->getAppKernelFacade());
    }

    public function getAppKernelFacade(): AppMerchantToAppKernelFacadeInterface
    {
        /** @phpstan-var \Spryker\Zed\AppMerchant\Dependency\Facade\AppMerchantToAppKernelFacadeInterface */
        return $this->getProvidedDependency(AppMerchantDependencyProvider::FACADE_APP_KERNEL);
    }

    public function createWebhookHandler(): WebhookHandler
    {
        return new WebhookHandler(
            $this->getPlatformProviderPlugin(),
            $this->createWebhookRequestExtender(),
            $this->getRepository(),
            $this->getEntityManager(),
            $this->createMessageSender(),
        );
    }

    public function createWebhookRequestExtender(): WebhookRequestExtender
    {
        return new WebhookRequestExtender(
            $this->createAppConfigLoader(),
            $this->getRepository(),
        );
    }

    public function createPaymentTransmissionsRequestExtender(): PaymentTransmissionsRequestExtender
    {
        return new PaymentTransmissionsRequestExtender($this->getRepository());
    }
}
