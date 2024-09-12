<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\AppMerchantBackendApi\Dependency\Facade;

class AppMerchantBackendApiToTranslatorFacadeBridge implements AppMerchantBackendApiToTranslatorFacadeInterface
{
    /**
     * @param \Spryker\Zed\Translator\Business\TranslatorFacadeInterface $translatorFacade
     */
    public function __construct(protected $translatorFacade)
    {
    }

    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->translatorFacade->trans($id, $parameters, $domain, $locale);
    }
}
