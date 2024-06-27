<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Plugin\GlueJsonApiConvention;

use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueRequestValidationTransfer;
use Spryker\Glue\AppMerchantBackendApi\Plugin\GlueApplication\MerchantAppOnboardingBackendApiRouteProviderPlugin;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RequestValidatorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Symfony\Component\HttpFoundation\Response;

class ValidateMerchantAppOnboardingRequestValidatorPlugin extends AbstractPlugin implements RequestValidatorPluginInterface
{
    public function validate(GlueRequestTransfer $glueRequestTransfer): GlueRequestValidationTransfer
    {
        $glueRequestValidationTransfer = new GlueRequestValidationTransfer();
        $glueRequestValidationTransfer->setIsValid(true);

        if ($glueRequestTransfer->getPath() !== MerchantAppOnboardingBackendApiRouteProviderPlugin::ROUTE_URL_PATH) {
            // @codeCoverageIgnoreStart
            // We currently only have one endpoint this case will never be reached
            return $glueRequestValidationTransfer;
            // @codeCoverageIgnoreEnd
        }

        if (!isset($glueRequestTransfer->getMeta()['x-tenant-identifier']) || !isset($glueRequestTransfer->getMeta()['x-merchant-reference'])) {
            $glueRequestValidationTransfer->setIsValid(false);
            $glueRequestValidationTransfer->setStatus(Response::HTTP_BAD_REQUEST);

            $glueRequestValidationTransfer->addError((new GlueErrorTransfer())
                ->setStatus(Response::HTTP_BAD_REQUEST)
                ->setMessage('You need to pass "x-tenant-identifier" and "x-merchant-reference" header to the request. One of or both are missing.'));

            return $glueRequestValidationTransfer;
        }

        return $glueRequestValidationTransfer;
    }
}
