<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business;

use Generated\Shared\Transfer\AppConfigTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingRequestTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\AppMerchant\Business\AppMerchantBusinessFactory getFactory()
 * @method \Spryker\Zed\AppMerchant\Persistence\AppMerchantRepositoryInterface getRepository()
 * @method \Spryker\Zed\AppMerchant\Persistence\AppMerchantEntityManagerInterface getEntityManager()
 */
class AppMerchantFacade extends AbstractFacade implements AppMerchantFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function informTenantAboutMerchantAppOnboardingReadiness(AppConfigTransfer $appConfigTransfer): AppConfigTransfer
    {
        return $this->getFactory()
            ->createMerchantAppOnboarding()
            ->informTenantAboutMerchantAppOnboardingReadiness($appConfigTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function createMerchantAppOnboarding(
        MerchantAppOnboardingRequestTransfer $merchantAppOnboardingRequestTransfer
    ): MerchantAppOnboardingResponseTransfer {
        return $this->getFactory()->createMerchantAppOnboardingCreator()->createMerchantAppOnboarding($merchantAppOnboardingRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function handleWebhook(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): WebhookResponseTransfer
    {
        return $this->getFactory()->createWebhookHandler()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function findMerchant(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer
    {
        return $this->getRepository()->findMerchant($merchantCriteriaTransfer);
    }
}
