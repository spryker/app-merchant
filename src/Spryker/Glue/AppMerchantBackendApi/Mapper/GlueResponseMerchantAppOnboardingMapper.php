<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Mapper;

use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Symfony\Component\HttpFoundation\Response;

class GlueResponseMerchantAppOnboardingMapper implements GlueResponseMerchantAppOnboardingMapperInterface
{
    public function mapMerchantAppOnboardingResponseTransferToSingleResourceGlueResponseTransfer(
        MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer
    ): GlueResponseTransfer {
        $glueResponseTransfer = new GlueResponseTransfer();

        return $this->addMerchantAppOnboardingResponseTransferToGlueResponse($merchantAppOnboardingResponseTransfer, $glueResponseTransfer);
    }

    public function addMerchantAppOnboardingResponseTransferToGlueResponse(
        MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer,
        GlueResponseTransfer $glueResponseTransfer
    ): GlueResponseTransfer {
        $merchantTransfer = $merchantAppOnboardingResponseTransfer->getMerchant();
        $merchantAppOnboarding = $merchantTransfer instanceof MerchantTransfer ? $merchantTransfer->toArray() : [];
        $merchantAppOnboarding[MerchantAppOnboardingResponseTransfer::STRATEGY] = $merchantAppOnboardingResponseTransfer->getStrategy();
        $merchantAppOnboarding[MerchantAppOnboardingResponseTransfer::URL] = $merchantAppOnboardingResponseTransfer->getUrl();

        $glueResponseTransfer->setContent((string)json_encode($merchantAppOnboarding));

        if ($merchantAppOnboardingResponseTransfer->getIsSuccessful() !== true) {
            $glueErrorTransfer = (new GlueErrorTransfer())->setMessage($merchantAppOnboardingResponseTransfer->getMessage());
            $glueResponseTransfer->setHttpStatus(Response::HTTP_PRECONDITION_FAILED);
            $glueResponseTransfer->addError($glueErrorTransfer);

            $glueResponseTransfer->setContent((string)json_encode(['errors' => [$glueErrorTransfer->toArray()]]));

            return $glueResponseTransfer;
        }

        // This is required to set the HTTP status code to 200 OK when the merchant is updated or 201 CREATED when it is created.
        if ($merchantTransfer->getIsNew() !== true) {
            $glueResponseTransfer->setHttpStatus(Response::HTTP_OK);
        }

        return $glueResponseTransfer;
    }
}
