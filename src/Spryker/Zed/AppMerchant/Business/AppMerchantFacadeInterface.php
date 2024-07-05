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
use Generated\Shared\Transfer\PaymentsTransmissionsRequestTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;

interface AppMerchantFacadeInterface
{
    /**
     * Specification:
     * - Check if the current Tenant was informed about the merchant onboarding readiness.
     * - If yes, abort further processing and return the `AppConfigTransfer`.
     * - If not:
     *  - Asks the App implementation for the merchant onboarding details (strategy, etc.) via Plugin.
     *  - Sends a message to the tenant about the merchant onboarding readiness.
     *  - Persists that the tenant was informed about the merchant onboarding readiness and the details for the onboarding process provided by the App implementation.
     *  - Returns the unchanged AppConfigTransfer (Only used).
     *
     * @api
     */
    public function informTenantAboutMerchantAppOnboardingReadiness(AppConfigTransfer $appConfigTransfer): AppConfigTransfer;

    /**
     * Specification:
     * - Persists a MerchantAppOnboarding into the storage.
     * - Uses `MerchantAppOnboardingValidatorInterface` to validate `MerchantAppOnboardingTransfer` before save.
     * - Executes pre-create `MerchantAppOnboardingCreatePluginInterface` before create the `MerchantAppOnboardingTransfer`.
     * - Executes post-create `MerchantAppOnboardingCreatePluginInterface` after create the `MerchantAppOnboardingTransfer`.
     * - Returns `MerchantAppOnboardingResponseTransfer`.
     *
     * @api
     */
    public function createMerchantAppOnboarding(
        MerchantAppOnboardingRequestTransfer $merchantAppOnboardingRequestTransfer
    ): MerchantAppOnboardingResponseTransfer;

    /**
     * Specification:
     * - Loads the `AppConfigTransfer` for the passed `tenantIdentifier`.
     * - Calls the `AppMerchantPlatformPluginInterface::handleWebhook()` method.
     * - When `AppMerchantPlatformPluginInterface::handleWebhook()` throws an exception, the exception is logged.
     * - When `AppMerchantPlatformPluginInterface::handleWebhook()` throws an exception, a `WebhookResponseTransfer` with a failed response is returned.
     * - When `AppMerchantPlatformPluginInterface::handleWebhook()` isSuccessful, a `WebhookResponseTransfer` with a successful response is returned.
     *
     * @api
     */
    public function handleWebhook(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): WebhookResponseTransfer;

    /**
     * Specification:
     * - Filters all order items that do not have a merchantReference.
     * - Groups order items by order reference and merchant reference.
     * - Extends the `PaymentsTransmissionsRequestTransfer` with the order items grouped by order reference and merchant reference.
     *
     * @api
     */
    public function extendPaymentsTransmissionsRequest(
        PaymentsTransmissionsRequestTransfer $paymentsTransmissionsRequestTransfer
    ): PaymentsTransmissionsRequestTransfer;

    /**
     * Specification:
     * - Finds a merchant created by {@link self::createMerchantAppOnboarding()} method.
     * - Requires merchantReference and tenantIdentifier to be set in the `MerchantCriteriaTransfer`.
     *
     * @api
     */
    public function findMerchant(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer;
}
