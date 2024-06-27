<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\AsyncApi\AppMerchant\AppMerchantTests\AppEvents;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AppConfigTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingStatusChangedTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Ramsey\Uuid\Uuid;
use Spryker\Zed\AppMerchant\AppMerchantConfig;
use Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding\MerchantAppOnboardingStatus;
use SprykerTest\AsyncApi\AppMerchant\AppMerchantAsyncApiTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group AsyncApi
 * @group AppMerchant
 * @group AppMerchantTests
 * @group AppEvents
 * @group MerchantAppOnboardingStatusChangedTest
 * Add your own group annotations below this line
 */
class MerchantAppOnboardingStatusChangedTest extends Unit
{
    /**
     * @var \SprykerTest\AsyncApi\AppMerchant\AppMerchantAsyncApiTester
     */
    protected AppMerchantAsyncApiTester $tester;

    /**
     * @return void
     */
    public function testGivenAMerchantWasOnboardedButNoOnboardingStatusPersistedWhenTheOnboardingStatusChangesThenTheMerchantAppOnboardingStatusChangedMessageIsSend(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->havePersistedAppConfigTransfer([
            AppConfigTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
        ]);

        $merchantOnboardingStatusChangedTransfer = $this->tester->haveMerchantAppOnboardingStatusChangedTransfer([
            MerchantAppOnboardingStatusChangedTransfer::MERCHANT_REFERENCE => $merchantReference,
            MerchantAppOnboardingStatusChangedTransfer::STATUS => MerchantAppOnboardingStatus::STATUS_INCOMPLETE,
        ]);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
        ]);

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setMerchantReference($merchantReference)
            ->setTenantIdentifier($tenantIdentifier);

        $this->tester->mockPlatformPluginImplementation();

        $merchantAppOnboardingTransfer = new MerchantAppOnboardingTransfer();
        $merchantAppOnboardingTransfer->setStatus(MerchantAppOnboardingStatus::STATUS_COMPLETED);

        $expectedWebhookResponseTransfer = new WebhookResponseTransfer();
        $expectedWebhookResponseTransfer
            ->setIsSuccessful(true)
            ->setMerchantAppOnboarding($merchantAppOnboardingTransfer);

        // Act
        $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $expectedWebhookResponseTransfer);

        // Assert
        $this->tester->assertMessageWasEmittedOnChannel($merchantOnboardingStatusChangedTransfer, 'merchant-app-events');
    }

    /**
     * @return void
     */
    public function testGivenAMerchantWasOnboardedWhenTheOnboardingStatusChangesThenTheMerchantAppOnboardingStatusChangedMessageIsSend(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->havePersistedAppConfigTransfer([
            AppConfigTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
        ]);

        $merchantOnboardingStatusChangedTransfer = $this->tester->haveMerchantAppOnboardingStatusChangedTransfer([
            MerchantAppOnboardingStatusChangedTransfer::MERCHANT_REFERENCE => $merchantReference,
            MerchantAppOnboardingStatusChangedTransfer::STATUS => MerchantAppOnboardingStatus::STATUS_INCOMPLETE,
        ]);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
            MerchantTransfer::CONFIG => [
                AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_INCOMPLETE,
            ],
        ]);

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setMerchantReference($merchantReference)
            ->setTenantIdentifier($tenantIdentifier);

        $this->tester->mockPlatformPluginImplementation();

        $merchantAppOnboardingTransfer = new MerchantAppOnboardingTransfer();
        $merchantAppOnboardingTransfer->setStatus(MerchantAppOnboardingStatus::STATUS_COMPLETED);

        $expectedWebhookResponseTransfer = new WebhookResponseTransfer();
        $expectedWebhookResponseTransfer
            ->setIsSuccessful(true)
            ->setMerchantAppOnboarding($merchantAppOnboardingTransfer);

        // Act
        $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $expectedWebhookResponseTransfer);

        // Assert
        $this->tester->assertMessageWasEmittedOnChannel($merchantOnboardingStatusChangedTransfer, 'merchant-app-events');
    }

    /**
     * @return void
     */
    public function testGivenAMerchantWasOnboardedAndTheConfigurationDoesntHaveTheMerchantOnboardingStatusSetWhenTheOnboardingStatusChangesThenTheMerchantAppOnboardingStatusChangedMessageIsSend(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->havePersistedAppConfigTransfer([
            AppConfigTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
        ]);

        $merchantOnboardingStatusChangedTransfer = $this->tester->haveMerchantAppOnboardingStatusChangedTransfer([
            MerchantAppOnboardingStatusChangedTransfer::MERCHANT_REFERENCE => $merchantReference,
            MerchantAppOnboardingStatusChangedTransfer::STATUS => MerchantAppOnboardingStatus::STATUS_INCOMPLETE,
        ]);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
            MerchantTransfer::CONFIG => [
                'foo' => 'bar',
            ],
        ]);

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setMerchantReference($merchantReference)
            ->setTenantIdentifier($tenantIdentifier);

        $this->tester->mockPlatformPluginImplementation();

        $merchantAppOnboardingTransfer = new MerchantAppOnboardingTransfer();
        $merchantAppOnboardingTransfer->setStatus(MerchantAppOnboardingStatus::STATUS_COMPLETED);

        $expectedWebhookResponseTransfer = new WebhookResponseTransfer();
        $expectedWebhookResponseTransfer
            ->setIsSuccessful(true)
            ->setMerchantAppOnboarding($merchantAppOnboardingTransfer);

        // Act
        $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $expectedWebhookResponseTransfer);

        // Assert
        $this->tester->assertMessageWasEmittedOnChannel($merchantOnboardingStatusChangedTransfer, 'merchant-app-events');
    }

    /**
     * @return void
     */
    public function testGivenAMerchantWasOnboardedWhenTheOnboardingStatusDoesNotChangeThenTheMerchantAppOnboardingStatusChangedMessageIsNotSend(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->havePersistedAppConfigTransfer([
            AppConfigTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
        ]);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
            MerchantTransfer::CONFIG => [
                AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_INCOMPLETE,
            ],
        ]);

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setMerchantReference($merchantReference)
            ->setTenantIdentifier($tenantIdentifier);

        $this->tester->mockPlatformPluginImplementation();

        $merchantAppOnboardingTransfer = new MerchantAppOnboardingTransfer();
        $merchantAppOnboardingTransfer->setStatus(MerchantAppOnboardingStatus::STATUS_INCOMPLETE);

        $expectedWebhookResponseTransfer = new WebhookResponseTransfer();
        $expectedWebhookResponseTransfer
            ->setIsSuccessful(true)
            ->setMerchantAppOnboarding($merchantAppOnboardingTransfer);

        // Act
        $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $expectedWebhookResponseTransfer);

        // Assert
        $this->tester->assertMessageWasNotSent(MerchantAppOnboardingStatusChangedTransfer::class);
    }

    /**
     * @return void
     */
    public function testGivenAMerchantWasOnboardedWhenThePlatformReturnsAFailedResponseThenTheMerchantAppOnboardingStatusChangedMessageIsNotSend(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->havePersistedAppConfigTransfer([
            AppConfigTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
        ]);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
            MerchantTransfer::CONFIG => [
                AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_INCOMPLETE,
            ],
        ]);

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setMerchantReference($merchantReference)
            ->setTenantIdentifier($tenantIdentifier);

        $this->tester->mockPlatformPluginImplementation();

        $expectedWebhookResponseTransfer = new WebhookResponseTransfer();
        $expectedWebhookResponseTransfer
            ->setIsSuccessful(false);

        // Act
        $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $expectedWebhookResponseTransfer);

        // Assert
        $this->tester->assertMessageWasNotSent(MerchantAppOnboardingStatusChangedTransfer::class);
    }
}
