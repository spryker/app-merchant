<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi;

use Spryker\Glue\AppMerchantBackendApi\Dependency\Facade\AppMerchantBackendApiToAppMerchantFacadeInterface;
use Spryker\Glue\AppMerchantBackendApi\Dependency\Facade\AppMerchantBackendApiToTranslatorFacadeInterface;
use Spryker\Glue\AppMerchantBackendApi\Mapper\GlueRequestMerchantAppOnboardingMapper;
use Spryker\Glue\AppMerchantBackendApi\Mapper\GlueRequestMerchantAppOnboardingMapperInterface;
use Spryker\Glue\AppMerchantBackendApi\Mapper\GlueResponseMerchantAppOnboardingMapper;
use Spryker\Glue\AppMerchantBackendApi\Mapper\GlueResponseMerchantAppOnboardingMapperInterface;
use Spryker\Glue\Kernel\Backend\AbstractFactory;

class AppMerchantBackendApiFactory extends AbstractFactory
{
    public function createGlueRequestMerchantAppOnboardingMapper(): GlueRequestMerchantAppOnboardingMapperInterface
    {
        return new GlueRequestMerchantAppOnboardingMapper();
    }

    public function createGlueResponseMerchantAppOnboardingMapper(): GlueResponseMerchantAppOnboardingMapperInterface
    {
        return new GlueResponseMerchantAppOnboardingMapper();
    }

    public function getAppMerchantFacade(): AppMerchantBackendApiToAppMerchantFacadeInterface
    {
        /** @phpstan-var \Spryker\Glue\AppMerchantBackendApi\Dependency\Facade\AppMerchantBackendApiToAppMerchantFacadeInterface */
        return $this->getProvidedDependency(AppMerchantBackendApiDependencyProvider::FACADE_APP_MERCHANT);
    }

    public function getTranslatorFacade(): AppMerchantBackendApiToTranslatorFacadeInterface
    {
        /** @phpstan-var \Spryker\Glue\AppMerchantBackendApi\Dependency\Facade\AppMerchantBackendApiToTranslatorFacadeInterface */
        return $this->getProvidedDependency(AppMerchantBackendApiDependencyProvider::FACADE_TRANSLATOR);
    }
}
