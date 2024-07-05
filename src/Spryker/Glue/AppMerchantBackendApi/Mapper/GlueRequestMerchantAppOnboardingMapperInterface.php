<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Mapper;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingRequestTransfer;

interface GlueRequestMerchantAppOnboardingMapperInterface
{
    public function mapGlueRequestTransferToMerchantAppOnboardingRequestTransfer(
        GlueRequestTransfer $glueRequestTransfer
    ): MerchantAppOnboardingRequestTransfer;
}
