<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Controller;

use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\Kernel\Backend\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @method \Spryker\Glue\AppMerchantBackendApi\AppMerchantBackendApiFactory getFactory()
 */
class MerchantAppOnboardingResourceController extends AbstractController
{
    public function postAction(GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        if (!$glueRequestTransfer->getContent()) {
            return (new GlueResponseTransfer())
                ->setHttpStatus(Response::HTTP_BAD_REQUEST)
                ->addError((new GlueErrorTransfer())->setMessage('POST content is required.'));
        }

        $merchantAppOnboardingRequestTransfer = $this->getFactory()->createGlueRequestMerchantAppOnboardingMapper()->mapGlueRequestTransferToMerchantAppOnboardingRequestTransfer($glueRequestTransfer);

        try {
            $merchantAppOnboardingResponseTransfer = $this->getFactory()->getAppMerchantFacade()->createMerchantAppOnboarding($merchantAppOnboardingRequestTransfer);
        } catch (Throwable $throwable) {
            return (new GlueResponseTransfer())
                ->setHttpStatus(Response::HTTP_BAD_REQUEST)
                ->addError((new GlueErrorTransfer())->setMessage($throwable->getMessage()));
        }

        return $this->getFactory()->createGlueResponseMerchantAppOnboardingMapper()->mapMerchantAppOnboardingResponseTransferToSingleResourceGlueResponseTransfer($merchantAppOnboardingResponseTransfer);
    }
}
