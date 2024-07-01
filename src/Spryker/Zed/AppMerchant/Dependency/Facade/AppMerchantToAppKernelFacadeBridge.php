<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Dependency\Facade;

use Generated\Shared\Transfer\AppConfigCriteriaTransfer;
use Generated\Shared\Transfer\AppConfigTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

class AppMerchantToAppKernelFacadeBridge implements AppMerchantToAppKernelFacadeInterface
{
    /**
     * @var \Spryker\Zed\AppKernel\Business\AppKernelFacadeInterface
     */
    protected $appKernelFacade;

    /**
     * @param \Spryker\Zed\AppKernel\Business\AppKernelFacadeInterface $appKernelFacade
     */
    public function __construct($appKernelFacade)
    {
        $this->appKernelFacade = $appKernelFacade;
    }

    public function getConfig(AppConfigCriteriaTransfer $appConfigCriteriaTransfer, TransferInterface $transfer): AppConfigTransfer
    {
        return $this->appKernelFacade->getConfig($appConfigCriteriaTransfer, $transfer);
    }
}
