<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Dependency\Facade;

use Generated\Shared\Transfer\MerchantAppOnboardingRequestTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;
use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;

class AppMerchantBackendApiToAppMerchantFacadeBridge implements AppMerchantBackendApiToAppMerchantFacadeInterface
{
    /**
     * @var \Spryker\Zed\AppMerchant\Business\AppMerchantFacadeInterface
     */
    protected $appMerchantFacade;

    /**
     * @param \Spryker\Zed\AppMerchant\Business\AppMerchantFacadeInterface $appMerchantFacade
     */
    public function __construct($appMerchantFacade)
    {
        $this->appMerchantFacade = $appMerchantFacade;
    }

    public function createMerchantAppOnboarding(
        MerchantAppOnboardingRequestTransfer $merchantAppOnboardingRequestTransfer
    ): MerchantAppOnboardingResponseTransfer {
        return $this->appMerchantFacade->createMerchantAppOnboarding($merchantAppOnboardingRequestTransfer);
    }

    public function getMerchantCollection(
        MerchantCriteriaTransfer $merchantCriteriaTransfer
    ): MerchantCollectionTransfer {
        return $this->appMerchantFacade->getMerchantCollection($merchantCriteriaTransfer);
    }
}
