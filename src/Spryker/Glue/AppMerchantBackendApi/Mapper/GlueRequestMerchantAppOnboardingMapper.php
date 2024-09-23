<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Mapper;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingRequestTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

class GlueRequestMerchantAppOnboardingMapper implements GlueRequestMerchantAppOnboardingMapperInterface
{
    public function mapGlueRequestTransferToMerchantAppOnboardingRequestTransfer(
        GlueRequestTransfer $glueRequestTransfer
    ): MerchantAppOnboardingRequestTransfer {
        $merchantTransfer = new MerchantTransfer();
        $merchantTransfer->fromArray($glueRequestTransfer->getAttributes(), true);
        $merchantTransfer->fromArray($glueRequestTransfer->getAttributes()['merchant'] ?? [], true);
        $merchantTransfer->setTenantIdentifier($glueRequestTransfer->getMeta()['x-tenant-identifier'][0] ?? '');
        $merchantTransfer->setMerchantReference($glueRequestTransfer->getMeta()['x-merchant-reference'][0] ?? '');

        $merchantAppOnboardingRequestTransfer = new MerchantAppOnboardingRequestTransfer();
        $merchantAppOnboardingRequestTransfer->fromArray($glueRequestTransfer->getAttributes(), true);
        $merchantAppOnboardingRequestTransfer
            ->setTenantIdentifier($merchantTransfer->getTenantIdentifier())
            ->setMerchant($merchantTransfer);

        return $merchantAppOnboardingRequestTransfer;
    }
}
