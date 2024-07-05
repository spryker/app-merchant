<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Dependency\Facade;

use Generated\Shared\Transfer\MerchantAppOnboardingRequestTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;

interface AppMerchantBackendApiToAppMerchantFacadeInterface
{
    public function createMerchantAppOnboarding(
        MerchantAppOnboardingRequestTransfer $merchantAppOnboardingRequestTransfer
    ): MerchantAppOnboardingResponseTransfer;
}
