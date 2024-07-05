<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding;

use Generated\Shared\Transfer\AppConfigTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingDetailsTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingRequestTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;

interface AppMerchantPlatformPluginInterface
{
    /**
     * Specification:
     * - Used for the first step of the onboarding process which is used to send details that the merchants will use to go through the onboarding process.
     * - Adds onboarding details the `MerchantAppOnboardingDetailsTransfer` for the platform specific onboarding process.
     * - Adds the `OnboardingTransfer` to the `MerchantAppOnboardingDetailsTransfer`.
     * - The `OnboardingTransfer` contains the strategy for the onboarding process.
     * - The `OnboardingTransfer` contains the URL for the onboarding process.
     * - Returns the `MerchantAppOnboardingDetailsTransfer`.
     *
     * @api
     */
    public function provideOnboardingDetails(
        AppConfigTransfer $appConfigTransfer,
        MerchantAppOnboardingDetailsTransfer $merchantAppOnboardingDetailsTransfer
    ): MerchantAppOnboardingDetailsTransfer;

    /**
     * Specification:
     * - Uses the data from the first step mentioned above.
     * - Passes the `MerchantAppOnboardingRequestTransfer` to the platform specific onboarding process.
     * - Passes the `MerchantAppOnboardingResponseTransfer` to the platform specific onboarding process.
     * - The `MerchantAppOnboardingRequestTransfer` contains the `MerchantTransfer`.
     * - The `MerchantAppOnboardingRequestTransfer` contains the `successUrl`.
     * - The `MerchantAppOnboardingRequestTransfer` contains the `errorUrl`.
     * - The `MerchantAppOnboardingRequestTransfer` contains the `cancelUrl`.
     * - Adds `MerchantAppOnboardingResponseTransfer::STRATEGY` which will be used on the Tenant side.
     * - [Optional] Adds `MerchantAppOnboardingResponseTransfer::URL` which will be used on the Tenant side when the strategy is either `iframe` or `redirect`.
     * - [Optional] Adds `MerchantAppOnboardingResponseTransfer::CONTENT` which will be used on the Tenant side when the strategy is `content`.
     * - Returns the `MerchantAppOnboardingResponseTransfer` with the result of the onboarding process.
     *
     * @api
     */
    public function onboardMerchant(
        MerchantAppOnboardingRequestTransfer $merchantAppOnboardingRequestTransfer,
        MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer
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
}
