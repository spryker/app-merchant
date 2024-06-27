<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\AppConfig;

use Generated\Shared\Transfer\AppConfigCriteriaTransfer;
use Generated\Shared\Transfer\AppConfigTransfer;
use Spryker\Zed\AppMerchant\Dependency\Facade\AppMerchantToAppKernelFacadeInterface;

class AppConfigLoader
{
    public function __construct(protected AppMerchantToAppKernelFacadeInterface $appMerchantToAppKernelFacade)
    {
    }

    public function loadAppConfig(string $tenantIdentifier): AppConfigTransfer
    {
        return $this->appMerchantToAppKernelFacade->getConfig(
            (new AppConfigCriteriaTransfer())->setTenantIdentifier($tenantIdentifier),
            new AppConfigTransfer(),
        );
    }
}
