<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant;

use Spryker\Glue\AppMerchantBackendApi\Plugin\GlueApplication\MerchantAppOnboardingBackendApiRouteProviderPlugin;
use Spryker\Shared\AppKernel\AppKernelConstants;
use Spryker\Shared\GlueJsonApiConvention\GlueJsonApiConventionConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class AppMerchantConfig extends AbstractBundleConfig
{
    /**
     * @api
     *
     * @var string
     */
    public const MERCHANT_ONBOARDING_STATUS = 'merchant-onboarding-status';

    public function getAppMerchantAppOnboardingApiUrl(): string
    {
        return $this->getStringValue(GlueJsonApiConventionConstants::GLUE_DOMAIN) . MerchantAppOnboardingBackendApiRouteProviderPlugin::ROUTE_URL_PATH;
    }

    public function getAppIdentifier(): string
    {
        return $this->getStringValue(AppKernelConstants::APP_IDENTIFIER);
    }

    // TODO can we move this to another place?
    // It should be provided by the "using" module.

    public function getOnboardingType(): string
    {
        return 'payment';
    }

    protected function getStringValue(string $configKey): string
    {
        /** @phpstan-var string */
        return $this->get($configKey);
    }
}
