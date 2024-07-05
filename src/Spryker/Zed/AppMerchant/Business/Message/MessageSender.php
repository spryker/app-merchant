<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\Message;

use Generated\Shared\Transfer\AppConfigTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingDetailsTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingStatusChangedTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\MessageAttributesTransfer;
use Generated\Shared\Transfer\ReadyForMerchantAppOnboardingTransfer;
use Spryker\Zed\AppMerchant\AppMerchantConfig;
use Spryker\Zed\MessageBroker\Business\MessageBrokerFacadeInterface;

class MessageSender
{
    public function __construct(protected MessageBrokerFacadeInterface $messageBrokerFacade, protected AppMerchantConfig $appMerchantConfig)
    {
    }

    public function sendReadyForMerchantAppOnboardingMessage(
        MerchantAppOnboardingDetailsTransfer $merchantAppOnboardingDetailsTransfer,
        AppConfigTransfer $appConfigTransfer
    ): void {
        $readyForMerchantAppOnboardingTransfer = new ReadyForMerchantAppOnboardingTransfer();
        $readyForMerchantAppOnboardingTransfer->fromArray($merchantAppOnboardingDetailsTransfer->toArray(), true);

        $this->sendMessage($readyForMerchantAppOnboardingTransfer, $appConfigTransfer);
    }

    public function sendMerchantAppOnboardingStatusChangedMessage(
        MerchantTransfer $merchantTransfer,
        AppConfigTransfer $appConfigTransfer
    ): void {
        $merchantOnboardingStatus = $merchantTransfer->getConfigOrFail()[AppMerchantConfig::MERCHANT_ONBOARDING_STATUS];

        $merchantAppOnboardingStatusChangedTransfer = new MerchantAppOnboardingStatusChangedTransfer();
        $merchantAppOnboardingStatusChangedTransfer
            ->setStatus($merchantOnboardingStatus)
            ->setAppIdentifier($this->appMerchantConfig->getAppIdentifier())
            ->setMerchantReference($merchantTransfer->getMerchantReferenceOrFail())
            ->setType($this->appMerchantConfig->getOnboardingType());

        $this->sendMessage($merchantAppOnboardingStatusChangedTransfer, $appConfigTransfer);
    }

    protected function sendMessage(
        ReadyForMerchantAppOnboardingTransfer|MerchantAppOnboardingStatusChangedTransfer $messageTransfer,
        AppConfigTransfer $appConfigTransfer
    ): void {
        $messageTransfer->setMessageAttributes($this->getMessageAttributes(
            $appConfigTransfer->getTenantIdentifierOrFail(),
            $messageTransfer::class,
        ));

        $this->messageBrokerFacade->sendMessage($messageTransfer);
    }

    protected function getMessageAttributes(string $tenantIdentifier, string $transferName): MessageAttributesTransfer
    {
        $messageAttributesTransfer = new MessageAttributesTransfer();
        $messageAttributesTransfer
            ->setActorId($this->appMerchantConfig->getAppIdentifier())
            ->setEmitter($this->appMerchantConfig->getAppIdentifier())
            ->setTenantIdentifier($tenantIdentifier)
            ->setStoreReference($tenantIdentifier)
            ->setTransferName($transferName);

        return $messageAttributesTransfer;
    }
}
