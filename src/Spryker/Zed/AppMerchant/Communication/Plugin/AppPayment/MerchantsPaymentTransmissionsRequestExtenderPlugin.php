<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Communication\Plugin\AppPayment;

use Generated\Shared\Transfer\PaymentTransmissionsRequestTransfer;
use Spryker\Zed\AppPayment\Dependency\Plugin\PaymentTransmissionsRequestExtenderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * {@inheritDoc}
 *
 * @api
 *
 * @method \Spryker\Zed\AppMerchant\Business\AppMerchantFacadeInterface getFacade()
 * @method \Spryker\Zed\AppMerchant\AppMerchantConfig getConfig()
 */
class MerchantsPaymentTransmissionsRequestExtenderPlugin extends AbstractPlugin implements PaymentTransmissionsRequestExtenderPluginInterface
{
    public function extendPaymentTransmissionsRequest(
        PaymentTransmissionsRequestTransfer $paymentTransmissionsRequestTransfer
    ): PaymentTransmissionsRequestTransfer {
        return $this->getFacade()->extendPaymentTransmissionsRequest($paymentTransmissionsRequestTransfer);
    }
}
