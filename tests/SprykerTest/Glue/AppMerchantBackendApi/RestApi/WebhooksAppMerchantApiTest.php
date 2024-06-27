<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Glue\AppMerchantBackendApi\RestApi;

use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Generated\Shared\Transfer\AppConfigTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Ramsey\Uuid\Uuid;
use Spryker\Zed\AppMerchant\AppMerchantConfig;
use Spryker\Zed\AppMerchant\AppMerchantDependencyProvider;
use Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding\MerchantAppOnboardingStatus;
use Spryker\Zed\AppMerchant\Business\Message\MessageBuilder;
use Spryker\Zed\AppMerchant\Communication\Plugin\AppWebhook\AccountUpdatedWebhookHandlerPlugin;
use Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface;
use Spryker\Zed\AppPayment\Business\Payment\Webhook\WebhookDataType;
use Spryker\Zed\AppWebhook\AppWebhookDependencyProvider;
use SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester;
use SprykerTest\Shared\Testify\Helper\DependencyHelperTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group AppMerchantBackendApi
 * @group RestApi
 * @group WebhooksAppMerchantApiTest
 * Add your own group annotations below this line
 */
class WebhooksAppMerchantApiTest extends Unit
{
    use DependencyHelperTrait;

    protected AppMerchantBackendApiTester $tester;

    protected function _setUp(): void
    {
        parent::_setUp();

        // Ensure we only test against "our" known Plugin.
        $this->getDependencyHelper()->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [
            new AccountUpdatedWebhookHandlerPlugin(),
        ]);
    }

    public function testGivenMerchantIsOnboardedAndStateIsInIncompleteWhenThePlatformPluginReturnsASuccessfulResponseAndOnboardingStatusCompletedThenOnboardingIsMovedToCompleted(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->haveAppConfigForTenant($tenantIdentifier);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::CONFIG => [
                AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_INCOMPLETE,
            ],
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
        ]);

        $this->tester->mockGlueRequestWebhookMapperPlugin(WebhookDataType::ACCOUNT, $merchantReference, $tenantIdentifier);
        $this->mockPlatformPlugin(true, MerchantAppOnboardingStatus::STATUS_COMPLETED);

        // Act
        $this->tester->sendPost($this->tester->buildWebhookUrl(), ['web hook content received from third party payment provider']);

        // Assert
        $this->tester->seeResponseCodeIs(Response::HTTP_OK);

        $expectedMerchantTransfer = new MerchantTransfer();
        $expectedMerchantTransfer
            ->setConfig([AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_COMPLETED])
            ->setTenantIdentifier($tenantIdentifier)
            ->setMerchantReference($merchantReference);

        $this->tester->seeMerchantAppOnboardingStatus($expectedMerchantTransfer);
    }

    public function testGivenMerchantIsOnboardedAndStateIsInRequiresActionWhenThePlatformPluginReturnsASuccessfulResponseAndOnboardingStatusCompletedThenOnboardingIsMovedToCompleted(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->haveAppConfigForTenant($tenantIdentifier);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::CONFIG => [
                AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_PENDING,
            ],
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
        ]);

        $this->tester->mockGlueRequestWebhookMapperPlugin(WebhookDataType::ACCOUNT, $merchantReference, $tenantIdentifier);
        $this->mockPlatformPlugin(true, MerchantAppOnboardingStatus::STATUS_COMPLETED);

        // Act
        $this->tester->sendPost($this->tester->buildWebhookUrl(), ['web hook content received from third party payment provider']);

        // Assert
        $this->tester->seeResponseCodeIs(Response::HTTP_OK);

        $expectedMerchantTransfer = new MerchantTransfer();
        $expectedMerchantTransfer
            ->setConfig([AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_COMPLETED])
            ->setTenantIdentifier($tenantIdentifier)
            ->setMerchantReference($merchantReference);

        $this->tester->seeMerchantAppOnboardingStatus($expectedMerchantTransfer);
    }

    public function testGivenMerchantIsOnboardedAndStateIsInCompletedWhenThePlatformPluginReturnsASuccessfulResponseAndOnboardingStatusRequiresActionThenOnboardingIsMovedToRequiresAction(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->haveAppConfigForTenant($tenantIdentifier);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::CONFIG => [
                AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_COMPLETED,
            ],
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
        ]);

        $this->tester->mockGlueRequestWebhookMapperPlugin(WebhookDataType::ACCOUNT, $merchantReference, $tenantIdentifier);
        $this->mockPlatformPlugin(true, MerchantAppOnboardingStatus::STATUS_PENDING);

        // Act
        $this->tester->sendPost($this->tester->buildWebhookUrl(), ['web hook content received from third party payment provider']);

        // Assert
        $this->tester->seeResponseCodeIs(Response::HTTP_OK);

        $expectedMerchantTransfer = new MerchantTransfer();
        $expectedMerchantTransfer
            ->setConfig([AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_PENDING])
            ->setTenantIdentifier($tenantIdentifier)
            ->setMerchantReference($merchantReference);

        $this->tester->seeMerchantAppOnboardingStatus($expectedMerchantTransfer);
    }

    public function testWhenThePlatformPluginThrowsAnExceptionTheResponseCodeIs400(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->haveAppConfigForTenant($tenantIdentifier);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::CONFIG => [
                AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_COMPLETED,
            ],
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
        ]);

        $this->tester->mockGlueRequestWebhookMapperPlugin(WebhookDataType::ACCOUNT, $merchantReference, $tenantIdentifier);
        $this->mockPlatformPluginThatThrowsAnException();

        // Act
        $this->tester->sendPost($this->tester->buildWebhookUrl(), ['web hook content received from third party payment provider']);

        // Assert
        $this->tester->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $this->tester->seeResponseContainsErrorMessage('AppMerchantPlatformPluginInterface::handleWebhook() exception.');
    }

    public function testWhenThePlatformPluginReturnsAFailedResponseTheResponseCodeIs400(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->haveAppConfigForTenant($tenantIdentifier);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::CONFIG => [
                AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_COMPLETED,
            ],
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
        ]);

        $this->tester->mockGlueRequestWebhookMapperPlugin(WebhookDataType::ACCOUNT, $merchantReference, $tenantIdentifier);
        $this->mockPlatformPlugin(false);

        // Act
        $this->tester->sendPost($this->tester->buildWebhookUrl(), ['web hook content received from third party payment provider']);

        // Assert
        $this->tester->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
    }

    public function testWhenTheMerchantReferenceIsNotFoundInTheWebhookRequestTheResponseCodeIs400(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->haveAppConfigForTenant($tenantIdentifier);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::CONFIG => [
                AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_COMPLETED,
            ],
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
        ]);

        $this->tester->mockGlueRequestWebhookMapperPlugin(WebhookDataType::ACCOUNT, null, $tenantIdentifier);
        $this->mockPlatformPlugin(false);

        // Act
        $this->tester->sendPost($this->tester->buildWebhookUrl(), ['web hook content received from third party payment provider']);

        // Assert
        $this->tester->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $this->tester->seeResponseContainsErrorMessage(MessageBuilder::couldNotFindAMerchantReference());
    }

    public function testWhenTheTenantIdentifierIsNotFoundInTheWebhookRequestTheResponseCodeIs400(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->haveAppConfigForTenant($tenantIdentifier);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::CONFIG => [
                AppMerchantConfig::MERCHANT_ONBOARDING_STATUS => MerchantAppOnboardingStatus::STATUS_COMPLETED,
            ],
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
        ]);

        $this->tester->mockGlueRequestWebhookMapperPlugin(WebhookDataType::ACCOUNT, $merchantReference, null);
        $this->mockPlatformPlugin(false);

        // Act
        $this->tester->sendPost($this->tester->buildWebhookUrl(), ['web hook content received from third party payment provider']);

        // Assert
        $this->tester->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $this->tester->seeResponseContainsErrorMessage(MessageBuilder::couldNotFindATenantIdentifier());
    }

    public function testWhenNoMerchantCanBeFoundTheResponseCodeIs400(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $merchantReference = Uuid::uuid4()->toString();

        $this->tester->haveAppConfigForTenant($tenantIdentifier);

        $this->tester->mockGlueRequestWebhookMapperPlugin(WebhookDataType::ACCOUNT, $merchantReference, $tenantIdentifier);
        $this->mockPlatformPlugin(false);

        // Act
        $this->tester->sendPost($this->tester->buildWebhookUrl(), ['web hook content received from third party payment provider']);

        // Assert
        $this->tester->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $this->tester->seeResponseContainsErrorMessage(MessageBuilder::couldNotFindAMerchant($merchantReference, $tenantIdentifier));
    }

    protected function mockPlatformPlugin(bool $webhookResponseSuccessful, ?string $onboardingStatus = null): void
    {
        $platformPluginMock = Stub::makeEmpty(AppMerchantPlatformPluginInterface::class, [
            'handleWebhook' => function (WebhookRequestTransfer $webhookRequestTransfer) use ($webhookResponseSuccessful, $onboardingStatus): WebhookResponseTransfer {
                $merchantAppOnboardingTransfer = new MerchantAppOnboardingTransfer();
                $merchantAppOnboardingTransfer
                    ->setStatus($onboardingStatus);

                $webhookResponseTransfer = new WebhookResponseTransfer();
                $webhookResponseTransfer->setIsSuccessful($webhookResponseSuccessful);
                $webhookResponseTransfer->setMerchantAppOnboarding($merchantAppOnboardingTransfer);

                // Ensure that the AppConfig is always passed to the platform plugin.
                $this->assertInstanceOf(AppConfigTransfer::class, $webhookRequestTransfer->getAppConfig());

                // Ensure that the MerchantTransfer::merchantReference is always passed to the platform plugin.
                $this->assertNotNull($webhookRequestTransfer->getMerchantReference());

                return $webhookResponseTransfer;
            },
        ]);

        $this->getDependencyHelper()->setDependency(AppMerchantDependencyProvider::PLUGIN_PLATFORM, $platformPluginMock);
    }

    protected function mockPlatformPluginThatThrowsAnException(): void
    {
        $platformPluginMock = Stub::makeEmpty(AppMerchantPlatformPluginInterface::class, [
            'handleWebhook' => function (WebhookRequestTransfer $webhookRequestTransfer): WebhookResponseTransfer {
                throw new Exception('AppMerchantPlatformPluginInterface::handleWebhook() exception.');
            },
        ]);

        $this->getDependencyHelper()->setDependency(AppMerchantDependencyProvider::PLUGIN_PLATFORM, $platformPluginMock);
    }
}
