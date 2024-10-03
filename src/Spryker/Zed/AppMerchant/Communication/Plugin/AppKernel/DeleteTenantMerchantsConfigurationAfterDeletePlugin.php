<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Communication\Plugin\AppKernel;

use Generated\Shared\Transfer\AppConfigTransfer;
use Spryker\Zed\AppKernelExtension\Dependency\Plugin\ConfigurationAfterDeletePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\AppMerchant\AppMerchantConfig getConfig()
 * @method \Spryker\Zed\AppMerchant\Business\AppMerchantFacadeInterface getFacade()
 * @method \Spryker\Zed\AppMerchant\Business\AppMerchantBusinessFactory getFactory()
 */
class DeleteTenantMerchantsConfigurationAfterDeletePlugin extends AbstractPlugin implements ConfigurationAfterDeletePluginInterface
{
    /**
     * {@inheritDoc}
     * - Is provided to remove all merchants related to the tenant after the app disconnection, which is not necessary
     *      in production environments to keep data consistency, but can be useful for development purposes.
     * - Validates if the tenant merchants deletion after the app disconnection is enabled.
     * - Deletes all the merchants related to the tenant after the app disconnection.
     *
     * @api
     */
    public function afterDelete(AppConfigTransfer $appConfigTransfer): AppConfigTransfer
    {
        if (!$this->getConfig()->getIsTenantMerchantsDeletionAfterDisconnectionEnabled()) {
            return $appConfigTransfer;
        }

        $this->getFacade()->deleteMerchantByAppConfig($appConfigTransfer);

        return $appConfigTransfer;
    }
}
