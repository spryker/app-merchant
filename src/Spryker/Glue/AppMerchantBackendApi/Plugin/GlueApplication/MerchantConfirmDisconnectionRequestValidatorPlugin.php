<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Plugin\GlueApplication;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueRequestValidationTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Spryker\Glue\AppKernel\Plugin\GlueApplication\AbstractConfirmDisconnectionRequestValidatorPlugin;
use Spryker\Glue\AppMerchantBackendApi\AppMerchantBackendApiConfig;

/**
 * @method \Spryker\Glue\AppMerchantBackendApi\AppMerchantBackendApiFactory getFactory()
 */
class MerchantConfirmDisconnectionRequestValidatorPlugin extends AbstractConfirmDisconnectionRequestValidatorPlugin
{
    protected function validateDisconnectionRequest(
        GlueRequestTransfer $glueRequestTransfer,
        string $tenantIdentifier
    ): GlueRequestValidationTransfer {
        $merchantCollectionTransfer = $this->getFactory()->getAppMerchantFacade()->getMerchantCollection(
            (new MerchantCriteriaTransfer())
                ->setTenantIdentifier($tenantIdentifier),
        );

        if ($merchantCollectionTransfer->getMerchants()->count() === 0) {
            return (new GlueRequestValidationTransfer())
                ->setIsValid(true);
        }

        return $this->getFailedGlueRequestValidationTransfer(
            AppMerchantBackendApiConfig::ERROR_CODE_PAYMENT_DISCONNECTION_CANNOT_BE_PROCEEDED,
            $this->getFactory()->getTranslatorFacade()->trans('The payment App cannot be disconnected when there are active Merchants connected to the Marketplace. Merchants may not receive payout if you delete the App. Disconnect the merchants to continue.'),
        );
    }

    protected function getCancellationErrorCode(): string
    {
        return AppMerchantBackendApiConfig::ERROR_CODE_PAYMENT_DISCONNECTION_FORBIDDEN;
    }

    protected function getCancellationErrorMessage(): string
    {
        return $this->getFactory()->getTranslatorFacade()->trans('Please disconnect the merchants and try again.');
    }
}
