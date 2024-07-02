<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Communication\Plugin\AppWebhook;

use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * {@inheritDoc}
 *
 * @api
 *
 * @method \Spryker\Zed\AppMerchant\AppMerchantConfig getConfig()
 * @method \Spryker\Zed\AppMerchant\Business\AppMerchantFacadeInterface getFacade()
 */
class AccountUpdatedWebhookHandlerPlugin extends AbstractPlugin implements WebhookHandlerPluginInterface
{
    public function canHandle(WebhookRequestTransfer $webhookRequestTransfer): bool
    {
        return in_array($webhookRequestTransfer->getType(), $this->getConfig()->getHandleableWebhookTypes());
    }

    public function handleWebhook(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): WebhookResponseTransfer
    {
        return $this->getFacade()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);
    }
}
