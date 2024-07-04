<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Communication\Plugin\AppKernel;

use Generated\Shared\Transfer\AppConfigTransfer;
use Spryker\Zed\AppKernelExtension\Dependency\Plugin\ConfigurationAfterSavePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * {@inheritDoc}
 *
 * @api
 *
 * @method \Spryker\Zed\AppMerchant\Business\AppMerchantFacadeInterface getFacade()
 * @method \Spryker\Zed\AppMerchant\AppMerchantConfig getConfig()
 */
class InformTenantAboutMerchantAppOnboardingReadinessConfigurationAfterSavePlugin extends AbstractPlugin implements ConfigurationAfterSavePluginInterface
{
    public function afterSave(AppConfigTransfer $appConfigTransfer): AppConfigTransfer
    {
        return $this->getFacade()->informTenantAboutMerchantAppOnboardingReadiness($appConfigTransfer);
    }
}
