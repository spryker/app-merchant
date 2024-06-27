<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\Webhook;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Spryker\Zed\AppMerchant\Business\AppConfig\AppConfigLoader;
use Spryker\Zed\AppMerchant\Business\Exception\MerchantNotFoundException;
use Spryker\Zed\AppMerchant\Business\Message\MessageBuilder;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantRepositoryInterface;

class WebhookRequestExtender
{
    public function __construct(
        protected AppConfigLoader $appConfigLoader,
        protected AppMerchantRepositoryInterface $appMerchantRepository
    ) {
    }

    public function extendWebhookRequestTransfer(WebhookRequestTransfer $webhookRequestTransfer): WebhookRequestTransfer
    {
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer
            ->setMerchantReference($webhookRequestTransfer->getMerchantReferenceOrFail())
            ->setTenantIdentifier($webhookRequestTransfer->getTenantIdentifierOrFail());

        $merchantTransfer = $this->appMerchantRepository->findMerchant($merchantCriteriaTransfer);

        if (!$merchantTransfer instanceof MerchantTransfer) {
            throw new MerchantNotFoundException(MessageBuilder::couldNotFindAMerchant(
                $webhookRequestTransfer->getMerchantReferenceOrFail(),
                $webhookRequestTransfer->getTenantIdentifierOrFail(),
            ));
        }

        $appConfigTransfer = $this->appConfigLoader->loadAppConfig($merchantTransfer->getTenantIdentifierOrFail());

        $webhookRequestTransfer->setAppConfigOrFail($appConfigTransfer);

        return $webhookRequestTransfer;
    }
}
