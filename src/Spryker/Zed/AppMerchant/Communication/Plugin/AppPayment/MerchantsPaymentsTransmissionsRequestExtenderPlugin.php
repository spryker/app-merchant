<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Communication\Plugin\AppPayment;

use Generated\Shared\Transfer\PaymentsTransmissionsRequestTransfer;
use Spryker\Zed\AppPayment\Dependency\Plugin\PaymentsTransmissionsRequestExtenderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * {@inheritDoc}
 *
 * @api
 *
 * @method \Spryker\Zed\AppMerchant\Business\AppMerchantFacadeInterface getFacade()
 * @method \Spryker\Zed\AppMerchant\AppMerchantConfig getConfig()
 */
class MerchantsPaymentsTransmissionsRequestExtenderPlugin extends AbstractPlugin implements PaymentsTransmissionsRequestExtenderPluginInterface
{
    public function extendPaymentsTransmissionsRequest(
        PaymentsTransmissionsRequestTransfer $paymentsTransmissionsRequestTransfer
    ): PaymentsTransmissionsRequestTransfer {
        return $this->getFacade()->extendPaymentsTransmissionsRequest($paymentsTransmissionsRequestTransfer);
    }
}
