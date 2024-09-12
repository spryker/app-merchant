<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Plugin\GlueApplication;

use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueRequestValidationTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Spryker\Glue\AppKernel\Plugin\GlueApplication\AbstractConfirmDisconnectionRequestValidatorPlugin;
use Spryker\Glue\AppMerchantBackendApi\AppMerchantBackendApiConfig;
use Spryker\Glue\AppPaymentBackendApi\Mapper\Payment\GlueRequestPaymentMapper;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \Spryker\Glue\AppMerchantBackendApi\AppMerchantBackendApiFactory getFactory()
 */
class MerchantConfirmDisconnectionRequestValidatorPlugin extends AbstractConfirmDisconnectionRequestValidatorPlugin
{
    protected function getLabelOk(): string
    {
        return $this->getFactory()->getTranslatorFacade()->trans('Ignore & Disconnect');
    }

    protected function getLabelCancel(): string
    {
        return $this->getFactory()->getTranslatorFacade()->trans('Cancel');
    }

    protected function validateDisconnectionRequest(GlueRequestTransfer $glueRequestTransfer): GlueRequestValidationTransfer
    {
        if (empty($glueRequestTransfer->getMeta()[GlueRequestPaymentMapper::HEADER_TENANT_IDENTIFIER])) {
            return (new GlueRequestValidationTransfer())
                ->setIsValid(false)
                ->addError(
                    (new GlueErrorTransfer())
                        ->setCode(AppMerchantBackendApiConfig::ERROR_CODE_PAYMENT_DISCONNECTION_TENANT_IDENTIFIER_MISSING)
                        ->setMessage(
                            $this->getFactory()->getTranslatorFacade()->trans('Tenant identifier is missing.'),
                        ),
                );
        }

        $merchantCollectionTransfer = $this->getFactory()->getAppMerchantFacade()->getMerchantCollection(
            (new MerchantCriteriaTransfer())
                ->setTenantIdentifier($glueRequestTransfer->getMeta()[GlueRequestPaymentMapper::HEADER_TENANT_IDENTIFIER][0]),
        );

        if ($merchantCollectionTransfer->getMerchants()->count() === 0) {
            return (new GlueRequestValidationTransfer())
                ->setIsValid(true);
        }

        return (new GlueRequestValidationTransfer())
            ->setIsValid(false)
            ->addError(
                (new GlueErrorTransfer())
                    ->setCode(AppMerchantBackendApiConfig::ERROR_CODE_PAYMENT_DISCONNECTION_CANNOT_BE_PROCEEDED)
                    ->setMessage($this->getConfirmationErrorMessage()),
            );
    }

    protected function onConfirmationOk(GlueRequestTransfer $glueRequestTransfer): GlueRequestValidationTransfer
    {
        return (new GlueRequestValidationTransfer())
            ->setIsValid(true);
    }

    protected function onConfirmationCancel(GlueRequestTransfer $glueRequestTransfer): GlueRequestValidationTransfer
    {
        return (new GlueRequestValidationTransfer())
            ->setIsValid(false)
            ->setStatus(Response::HTTP_BAD_REQUEST)
            ->addError(
                (new GlueErrorTransfer())
                    ->setCode(AppMerchantBackendApiConfig::ERROR_CODE_PAYMENT_DISCONNECTION_FORBIDDEN)
                    ->setStatus(Response::HTTP_BAD_REQUEST)
                    ->setMessage($this->getCancellationErrorMessage()),
            );
    }

    protected function getConfirmationErrorMessage(): string
    {
        return $this->getFactory()->getTranslatorFacade()->trans('The payment App cannot be disconnected when there are active Merchants connected to the Marketplace. Merchants may not receive payout if you delete the App. Disconnect the merchants to continue.');
    }

    protected function getCancellationErrorMessage(): string
    {
        return $this->getFactory()->getTranslatorFacade()->trans('Please disconnect the merchants and try again.');
    }
}
