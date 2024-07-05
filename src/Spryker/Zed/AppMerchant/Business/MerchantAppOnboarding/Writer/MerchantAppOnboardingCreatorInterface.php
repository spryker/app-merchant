<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding\Writer;

use Generated\Shared\Transfer\MerchantAppOnboardingRequestTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;

interface MerchantAppOnboardingCreatorInterface
{
    public function createMerchantAppOnboarding(
        MerchantAppOnboardingRequestTransfer $merchantAppOnboardingRequestTransfer
    ): MerchantAppOnboardingResponseTransfer;
}
