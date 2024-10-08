<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Mapper;

use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;

interface GlueResponseMerchantAppOnboardingMapperInterface
{
    public function mapMerchantAppOnboardingResponseTransferToSingleResourceGlueResponseTransfer(
        MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer
    ): GlueResponseTransfer;
}
