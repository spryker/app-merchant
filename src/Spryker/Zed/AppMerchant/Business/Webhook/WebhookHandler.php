<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\Webhook;

use Generated\Shared\Transfer\MerchantAppOnboardingTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\AppMerchant\AppMerchantConfig;
use Spryker\Zed\AppMerchant\Business\Exception\MerchantReferenceNotFoundException;
use Spryker\Zed\AppMerchant\Business\Exception\TenantIdentifierNotFoundException;
use Spryker\Zed\AppMerchant\Business\Message\MessageBuilder;
use Spryker\Zed\AppMerchant\Business\Message\MessageSender;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantEntityManagerInterface;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantRepositoryInterface;
use Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Throwable;

class WebhookHandler
{
    use TransactionTrait;
    use LoggerTrait;

    public function __construct(
        protected AppMerchantPlatformPluginInterface $appMerchantPlatformPlugin,
        protected WebhookRequestExtender $webhookRequestExtender,
        protected AppMerchantRepositoryInterface $appMerchantRepository,
        protected AppMerchantEntityManagerInterface $appMerchantEntityManager,
        protected MessageSender $messageSender
    ) {
    }

    public function handleWebhook(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): WebhookResponseTransfer
    {
        try {
            $webhookRequestTransfer = $this->extendWebhookRequestTransfer($webhookRequestTransfer);
            $webhookResponseTransfer = $this->appMerchantPlatformPlugin->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);
        } catch (Throwable $throwable) {
            $this->getLogger()->error(
                $throwable->getMessage(),
                [
                    'request' => $webhookRequestTransfer->toArray(),
                    WebhookRequestTransfer::TENANT_IDENTIFIER => $webhookRequestTransfer->getTenantIdentifier(),
                ],
            );
            $webhookResponseTransfer = new WebhookResponseTransfer();
            $webhookResponseTransfer
                ->setIsSuccessful(false)
                ->setMessage($throwable->getMessage());

            return $webhookResponseTransfer;
        }

        // Return a failed response when response transfer is not successful.
        // The message should already be set in the platform plugin.
        if ($webhookResponseTransfer->getIsSuccessful() !== true) {
            return $webhookResponseTransfer;
        }

        /** @phpstan-var \Generated\Shared\Transfer\WebhookResponseTransfer */
        return $this->getTransactionHandler()->handleTransaction(function () use ($webhookRequestTransfer, $webhookResponseTransfer) {
            $this->updateMerchantAppOnboardingStatus(
                $webhookRequestTransfer,
                $webhookResponseTransfer,
            );

            return $webhookResponseTransfer;
        });
    }

    protected function extendWebhookRequestTransfer(WebhookRequestTransfer $webhookRequestTransfer): WebhookRequestTransfer
    {
        $this->validateWebhookRequestTransfer($webhookRequestTransfer);

        return $this->webhookRequestExtender->extendWebhookRequestTransfer($webhookRequestTransfer);
    }

    private function validateWebhookRequestTransfer(WebhookRequestTransfer $webhookRequestTransfer): void
    {
        if ($webhookRequestTransfer->getMerchantReference() === null || $webhookRequestTransfer->getMerchantReference() === '') {
            throw new MerchantReferenceNotFoundException(MessageBuilder::couldNotFindAMerchantReference());
        }

        if ($webhookRequestTransfer->getTenantIdentifier() === null || $webhookRequestTransfer->getTenantIdentifier() === '') {
            throw new TenantIdentifierNotFoundException(MessageBuilder::couldNotFindATenantIdentifier());
        }
    }

    protected function updateMerchantAppOnboardingStatus(
        WebhookRequestTransfer $webhookRequestTransfer,
        WebhookResponseTransfer $webhookResponseTransfer
    ): void {
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer
            ->setMerchantReference($webhookRequestTransfer->getMerchantReferenceOrFail())
            ->setTenantIdentifier($webhookRequestTransfer->getTenantIdentifierOrFail());

        $merchantAppOnboardingTransfer = $webhookResponseTransfer->getMerchantAppOnboardingOrFail();
        $merchantTransfer = $this->appMerchantRepository->findMerchant($merchantCriteriaTransfer);

        if ($merchantTransfer instanceof MerchantTransfer && $this->requiresUpdate($merchantTransfer, $merchantAppOnboardingTransfer)) {
            $config = $merchantTransfer->getConfig() ?? [];
            $config[AppMerchantConfig::MERCHANT_ONBOARDING_STATUS] = $merchantAppOnboardingTransfer->getStatus();
            $merchantTransfer->setConfig($config);

            $this->appMerchantEntityManager->updateMerchant($merchantTransfer);

            $this->messageSender->sendMerchantAppOnboardingStatusChangedMessage($merchantTransfer, $webhookRequestTransfer->getAppConfigOrFail());
        }
    }

    protected function requiresUpdate(MerchantTransfer $merchantTransfer, MerchantAppOnboardingTransfer $merchantAppOnboardingTransfer): bool
    {
        if ($merchantTransfer->getConfig() === null || $merchantTransfer->getConfig() === []) {
            return true;
        }

        if (!isset($merchantTransfer->getConfig()[AppMerchantConfig::MERCHANT_ONBOARDING_STATUS])) {
            return true;
        }

        return $merchantTransfer->getConfig()[AppMerchantConfig::MERCHANT_ONBOARDING_STATUS] !== $merchantAppOnboardingTransfer->getStatus();
    }
}
