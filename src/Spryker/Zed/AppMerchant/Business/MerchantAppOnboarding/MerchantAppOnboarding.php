<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding;

use Generated\Shared\Transfer\AppConfigTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingDetailsTransfer;
use Spryker\Zed\AppMerchant\AppMerchantConfig;
use Spryker\Zed\AppMerchant\Business\Exception\AppMerchantAppOnboardingConfigurationException;
use Spryker\Zed\AppMerchant\Business\Message\AppMerchantMessage;
use Spryker\Zed\AppMerchant\Business\Message\MessageSender;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantEntityManagerInterface;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantRepositoryInterface;
use Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface;

class MerchantAppOnboarding
{
    /**
     * @var string
     */
    public const TENANT_ONBOARDING_STATUS_CONFIG_KEY = 'tenant-onboarding-status';

    public function __construct(
        protected AppMerchantConfig $appMerchantConfig,
        protected AppMerchantRepositoryInterface $appMerchantRepository,
        protected AppMerchantEntityManagerInterface $appMerchantEntityManager,
        protected AppMerchantPlatformPluginInterface $appMerchantPlatformPlugin,
        protected MessageSender $messageSender
    ) {
    }

    public function informTenantAboutMerchantAppOnboardingReadiness(AppConfigTransfer $appConfigTransfer): AppConfigTransfer
    {
        // This is step 1 of the onboarding process. Details how to onboard merchants to this App are provided here.
        // Those are then later used by Merchants to onboard themselves.
        $merchantAppOnboardingDetailsTransfer = $this->appMerchantPlatformPlugin->provideOnboardingDetails($appConfigTransfer, new MerchantAppOnboardingDetailsTransfer());
        $merchantAppOnboardingDetailsTransfer->setTenantIdentifier($appConfigTransfer->getTenantIdentifier());

        $onboardingTransfer = $merchantAppOnboardingDetailsTransfer->getOnboardingOrFail();

        // When the AppMerchant modules endpoint should be used for onboarding (platform implementation didn't set this on purpose), the URL will be set here and not inside the Platform implementation.
        if ($onboardingTransfer->getStrategy() === MerchantAppOnboardingStrategy::API && ($onboardingTransfer->getUrl() === null || $onboardingTransfer->getUrl() === '' || $onboardingTransfer->getUrl() === '0')) {
            $onboardingTransfer->setUrl($this->appMerchantConfig->getAppMerchantAppOnboardingApiUrl());
        }

        $this->validateMerchantAppOnboardingDetails($merchantAppOnboardingDetailsTransfer);

        $this->messageSender->sendReadyForMerchantAppOnboardingMessage($merchantAppOnboardingDetailsTransfer, $appConfigTransfer);

        return $appConfigTransfer;
    }

    /**
     * The tenant identifier is already indirect tested with the code before. When there is no tenant identifier, the onboarding details are not created.
     *
     * @throws \Spryker\Zed\AppMerchant\Business\Exception\AppMerchantAppOnboardingConfigurationException
     */
    protected function validateMerchantAppOnboardingDetails(MerchantAppOnboardingDetailsTransfer $merchantAppOnboardingDetailsTransfer): void
    {
        if ($merchantAppOnboardingDetailsTransfer->getAppName() === null || $merchantAppOnboardingDetailsTransfer->getAppName() === '' || $merchantAppOnboardingDetailsTransfer->getAppName() === '0') {
            throw new AppMerchantAppOnboardingConfigurationException(AppMerchantMessage::appNameNotDefinedExceptionMessage());
        }

        if ($merchantAppOnboardingDetailsTransfer->getAppIdentifier() === null || $merchantAppOnboardingDetailsTransfer->getAppIdentifier() === '' || $merchantAppOnboardingDetailsTransfer->getAppIdentifier() === '0') {
            throw new AppMerchantAppOnboardingConfigurationException(AppMerchantMessage::appIdentifierNotDefinedExceptionMessage());
        }

        if ($merchantAppOnboardingDetailsTransfer->getType() === null || $merchantAppOnboardingDetailsTransfer->getType() === '' || $merchantAppOnboardingDetailsTransfer->getType() === '0') {
            throw new AppMerchantAppOnboardingConfigurationException(AppMerchantMessage::typeNotDefinedExceptionMessage());
        }

        if ($merchantAppOnboardingDetailsTransfer->getOnboardingOrFail()->getStrategy() === null || $merchantAppOnboardingDetailsTransfer->getOnboardingOrFail()->getStrategy() === '' || $merchantAppOnboardingDetailsTransfer->getOnboardingOrFail()->getStrategy() === '0') {
            throw new AppMerchantAppOnboardingConfigurationException(AppMerchantMessage::onboardingStrategyNotDefinedExceptionMessage());
        }

        if ($merchantAppOnboardingDetailsTransfer->getOnboardingOrFail()->getUrl() === null || $merchantAppOnboardingDetailsTransfer->getOnboardingOrFail()->getUrl() === '' || $merchantAppOnboardingDetailsTransfer->getOnboardingOrFail()->getUrl() === '0') {
            throw new AppMerchantAppOnboardingConfigurationException(AppMerchantMessage::onboardingUrlNotDefinedExceptionMessage());
        }
    }
}
