<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding\Writer;

use Generated\Shared\Transfer\MerchantAppOnboardingRequestTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\AppMerchant\Business\AppConfig\AppConfigLoader;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantEntityManagerInterface;
use Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;

class MerchantAppOnboardingCreator implements MerchantAppOnboardingCreatorInterface
{
    use TransactionTrait;

    public function __construct(
        protected AppMerchantPlatformPluginInterface $appMerchantPlatformPlugin,
        protected AppMerchantEntityManagerInterface $appMerchantEntityManager,
        protected AppConfigLoader $appConfigLoader
    ) {
    }

    public function createMerchantAppOnboarding(
        MerchantAppOnboardingRequestTransfer $merchantAppOnboardingRequestTransfer
    ): MerchantAppOnboardingResponseTransfer {
        $merchantAppOnboardingResponseTransfer = new MerchantAppOnboardingResponseTransfer();

        /** @phpstan-var \Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer */
        return $this->getTransactionHandler()->handleTransaction(function () use ($merchantAppOnboardingRequestTransfer, $merchantAppOnboardingResponseTransfer): MerchantAppOnboardingResponseTransfer {
            return $this->executeCreateMerchantAppOnboardingTransaction($merchantAppOnboardingRequestTransfer, $merchantAppOnboardingResponseTransfer);
        });
    }

    protected function executeCreateMerchantAppOnboardingTransaction(
        MerchantAppOnboardingRequestTransfer $merchantAppOnboardingRequestTransfer,
        MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer
    ): MerchantAppOnboardingResponseTransfer {
        $merchantTransfer = $merchantAppOnboardingRequestTransfer->getMerchantOrFail();

        $persistedMerchantTransfer = $this->appMerchantEntityManager->saveMerchant($merchantTransfer);

        // We need to use a clone here to avoid modifying the original MerchantTransfer by reference.
        // When clone is not used and the original MerchantTransfer is modified, the changes will NOT be persisted to the database.
        $merchantAppOnboardingRequestTransfer->setMerchant(clone $persistedMerchantTransfer);

        $merchantAppOnboardingRequestTransfer = $this->addAppConfig($merchantAppOnboardingRequestTransfer);

        $merchantAppOnboardingResponseTransfer = $this->appMerchantPlatformPlugin->onboardMerchant(
            $merchantAppOnboardingRequestTransfer,
            $merchantAppOnboardingResponseTransfer,
        );

        $merchantTransfer = $merchantAppOnboardingResponseTransfer->getMerchant();

        // Platform implementation may update the Merchant configuration, so we need to save it after the platform plugin execution.
        if ($merchantTransfer instanceof MerchantTransfer && $merchantTransfer !== $persistedMerchantTransfer) {
            $this->appMerchantEntityManager->updateMerchant($merchantTransfer);
        }

        return $merchantAppOnboardingResponseTransfer;
    }

    protected function addAppConfig(MerchantAppOnboardingRequestTransfer $merchantAppOnboardingRequestTransfer): MerchantAppOnboardingRequestTransfer
    {
        $tenantIdentifier = $merchantAppOnboardingRequestTransfer->getTenantIdentifierOrFail();
        $appConfigTransfer = $this->appConfigLoader->loadAppConfig($tenantIdentifier);

        return $merchantAppOnboardingRequestTransfer->setAppConfig($appConfigTransfer);
    }
}
