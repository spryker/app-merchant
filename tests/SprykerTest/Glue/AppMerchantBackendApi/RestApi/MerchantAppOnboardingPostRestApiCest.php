<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Glue\AppMerchantBackendApi\RestApi;

use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\AppMerchant\Persistence\SpyMerchantQuery;
use Ramsey\Uuid\Uuid;
use SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group AppMerchantBackendApi
 * @group RestApi
 * @group MerchantAppOnboardingPostRestApiCest
 * Add your own group annotations below this line
 */
class MerchantAppOnboardingPostRestApiCest
{
    /**
     * @param \SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester $I
     *
     * @return void
     */
    public function requestMerchantAppOnboardingPostReturnsHttpResponseCode400BadRequestWhenTenantIdentifierOrMerchantReferenceAreMissing(
        AppMerchantBackendApiTester $I
    ): void {
        // Arrange
        $url = $I->buildMerchantAppOnboardingUrl();

        $I->addHeader('content-type', 'application/json');

        // Act
        $I->sendPost($url, []);

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    /**
     * @param \SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester $I
     *
     * @return void
     */
    public function requestMerchantAppOnboardingPostReturnsHttpResponseCode400BadRequestWhenNoContentWasProvided(AppMerchantBackendApiTester $I): void
    {
        // Arrange
        $url = $I->buildMerchantAppOnboardingUrl();

        $merchantReference = Uuid::uuid4()->toString();
        $tenantIdentifier = Uuid::uuid4()->toString();

        $I->addHeader('x-tenant-identifier', $tenantIdentifier);
        $I->addHeader('x-merchant-reference', $merchantReference);
        $I->addHeader('content-type', 'application/json');

        // Act
        $I->sendPost($url);

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    /**
     * @param \SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester $I
     *
     * @return void
     */
    public function requestMerchantAppOnboardingPostReturnsHttpResponseCode400BadRequestWhenNoAppConfigurationFound(AppMerchantBackendApiTester $I): void
    {
        // Arrange
        $url = $I->buildMerchantAppOnboardingUrl();

        $merchantReference = Uuid::uuid4()->toString();
        $tenantIdentifier = Uuid::uuid4()->toString();

        $I->addHeader('x-tenant-identifier', $tenantIdentifier);
        $I->addHeader('x-merchant-reference', $merchantReference);
        $I->addHeader('content-type', 'application/json');

        // Act
        $I->sendPost($url, ['merchant' => [], 'successUrl' => 'successUrl', 'errorUrl' => 'errorUrl', 'cancelUrl' => 'cancelUrl']);

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    /**
     * @param \SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester $I
     *
     * @return void
     */
    public function requestMerchantAppOnboardingPostReturnsHttpResponseCode201WhenTheMerchantIsCreatedTheFirstTime(AppMerchantBackendApiTester $I): void
    {
        // Arrange
        $merchantReference = Uuid::uuid4()->toString();
        $tenantIdentifier = Uuid::uuid4()->toString();

        $I->haveAppConfigForTenant($tenantIdentifier);

        $platformStrategy = 'strategy';
        $platformUrl = 'https://www.example.de';

        $merchantAppOnboardingResponseTransfer = new MerchantAppOnboardingResponseTransfer();
        $merchantAppOnboardingResponseTransfer
            ->setIsSuccessful(true)
            ->setStrategy($platformStrategy)
            ->setUrl($platformUrl);

        $I->mockPlatformPluginImplementationWithMerchantAppOnboardingResponseTransfer($merchantAppOnboardingResponseTransfer);

        $url = $I->buildMerchantAppOnboardingUrl();

        $I->addHeader('x-tenant-identifier', $tenantIdentifier);
        $I->addHeader('x-merchant-reference', $merchantReference);
        $I->addHeader('content-type', 'application/json');

        // Act
        $I->sendPost($url, ['merchant' => [], 'successUrl' => 'successUrl', 'errorUrl' => 'errorUrl', 'cancelUrl' => 'cancelUrl']);

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseJsonContainsMerchantAppOnboarding($tenantIdentifier, $merchantReference);

        // Cleanup
        $I->addCleanup(function () use ($tenantIdentifier, $merchantReference): void {
            SpyMerchantQuery::create()
                ->filterByTenantIdentifier($tenantIdentifier)
                ->filterByMerchantReference($merchantReference)
                ->delete();
        });
    }

    /**
     * @param \SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester $I
     *
     * @return void
     */
    public function requestMerchantAppOnboardingPostReturnsHttpResponseCode200AndPersistsMerchantWhenThePlatformMerchantAppOnboardingResponseContainsAChangedMerchant(
        AppMerchantBackendApiTester $I
    ): void {
        // Arrange
        $merchantReference = Uuid::uuid4()->toString();
        $tenantIdentifier = Uuid::uuid4()->toString();

        $I->haveAppConfigForTenant($tenantIdentifier);

        $platformStrategy = 'strategy';
        $platformUrl = 'https://www.example.de';

        $merchantAppOnboardingResponseTransfer = new MerchantAppOnboardingResponseTransfer();
        $merchantAppOnboardingResponseTransfer
            ->setIsSuccessful(true)
            ->setStrategy($platformStrategy)
            ->setUrl($platformUrl)
            ->setMerchant((new MerchantTransfer())
                ->setConfig(['foo' => 'bar'])
                ->setMerchantReference($merchantReference)
                ->setTenantIdentifier($tenantIdentifier)
                ->setIsNew(true));

        $I->mockPlatformPluginImplementationWithMerchantAppOnboardingResponseTransfer($merchantAppOnboardingResponseTransfer);

        $url = $I->buildMerchantAppOnboardingUrl();

        $I->addHeader('x-tenant-identifier', $tenantIdentifier);
        $I->addHeader('x-merchant-reference', $merchantReference);
        $I->addHeader('content-type', 'application/json');

        // Act
        $I->sendPost($url, ['merchant' => [], 'successUrl' => 'successUrl', 'errorUrl' => 'errorUrl', 'cancelUrl' => 'cancelUrl']);

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseJsonContainsMerchantAppOnboarding($tenantIdentifier, $merchantReference);

        $I->seeMerchantInDatabase($merchantReference, ['foo' => 'bar']);

        // Cleanup
        $I->addCleanup(function () use ($tenantIdentifier, $merchantReference): void {
            SpyMerchantQuery::create()
                ->filterByTenantIdentifier($tenantIdentifier)
                ->filterByMerchantReference($merchantReference)
                ->delete();
        });
    }

    /**
     * @param \SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester $I
     *
     * @return void
     */
    public function requestMerchantAppOnboardingPostReturnsA201ResponseWithTheOnboardingStrategyAndUrlThatIsImplementedByThePlatform(
        AppMerchantBackendApiTester $I
    ): void {
        // Arrange
        $merchantReference = Uuid::uuid4()->toString();
        $tenantIdentifier = Uuid::uuid4()->toString();

        $I->haveAppConfigForTenant($tenantIdentifier);

        $platformStrategy = 'strategy';
        $platformUrl = 'https://www.example.de';

        $merchantAppOnboardingResponseTransfer = new MerchantAppOnboardingResponseTransfer();
        $merchantAppOnboardingResponseTransfer
            ->setIsSuccessful(true)
            ->setStrategy($platformStrategy)
            ->setUrl($platformUrl);

        $I->mockPlatformPluginImplementationWithMerchantAppOnboardingResponseTransfer($merchantAppOnboardingResponseTransfer);

        $url = $I->buildMerchantAppOnboardingUrl();

        $I->addHeader('x-tenant-identifier', $tenantIdentifier);
        $I->addHeader('x-merchant-reference', $merchantReference);
        $I->addHeader('content-type', 'application/json');

        // Act
        $I->sendPost($url, ['merchant' => [], 'successUrl' => 'successUrl', 'errorUrl' => 'errorUrl', 'cancelUrl' => 'cancelUrl']);

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseJsonContainsStrategyAndUrlFromPlatformPluginImplementation($platformStrategy, $platformUrl);

        // Cleanup
        $I->addCleanup(function () use ($tenantIdentifier, $merchantReference): void {
            SpyMerchantQuery::create()
                ->filterByTenantIdentifier($tenantIdentifier)
                ->filterByMerchantReference($merchantReference)
                ->delete();
        });
    }

    /**
     * @param \SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester $I
     *
     * @return void
     */
    public function requestMerchantAppOnboardingPostReturnsHttpResponseCode200WhenTheMerchantWasAlreadyCreatedBefore(
        AppMerchantBackendApiTester $I
    ): void {
        // Arrange
        $merchantReference = Uuid::uuid4()->toString();
        $tenantIdentifier = Uuid::uuid4()->toString();

        $I->haveAppConfigForTenant($tenantIdentifier);

        $platformStrategy = 'strategy';
        $platformUrl = 'https://www.example.de';

        // We already know the merchant, and we must return the existing entity and a 200 status code
        $merchantTransfer = $I->haveMerchantPersisted([
            MerchantTransfer::MERCHANT_REFERENCE => $merchantReference,
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier,
        ]);

        $merchantAppOnboardingResponseTransfer = new MerchantAppOnboardingResponseTransfer();
        $merchantAppOnboardingResponseTransfer
            ->setIsSuccessful(true)
            ->setStrategy($platformStrategy)
            ->setUrl($platformUrl);

        $I->mockPlatformPluginImplementationWithMerchantAppOnboardingResponseTransfer($merchantAppOnboardingResponseTransfer);

        $url = $I->buildMerchantAppOnboardingUrl();

        $I->addHeader('x-tenant-identifier', $tenantIdentifier);
        $I->addHeader('x-merchant-reference', $merchantReference);
        $I->addHeader('content-type', 'application/json');

        // Act
        $I->sendPost($url, $merchantTransfer->toArray());

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseIsJson();
        $I->seeResponseJsonContainsMerchantAppOnboarding($tenantIdentifier, $merchantReference);
    }

    /**
     * @param \SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester $I
     *
     * @return void
     */
    public function requestMerchantAppOnboardingPostReturnsHttpResponseCode400WhenNoAppConfigCanBeFoundForTheTenant(AppMerchantBackendApiTester $I): void
    {
        // Arrange
        $merchantAppOnboardingTransfer = $I->haveMerchantAppOnboardingTransfer();
        $url = $I->buildMerchantAppOnboardingUrl();

        // Act
        $I->sendPost($url, $merchantAppOnboardingTransfer->toArray());

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param \SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester $I
     *
     * @return void
     */
    public function requestMerchantAppOnboardingPostReturnsHttpResponseCode400WhenNoContentProvidedInTheRequest(AppMerchantBackendApiTester $I): void
    {
        // Arrange
        $url = $I->buildMerchantAppOnboardingUrl();

        // Act
        $I->sendPost($url, []);

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
    }

    /**
     * This case could be when a Platform is not configured properly to be able to onboard merchants.
     *
     * @param \SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiTester $I
     *
     * @return void
     */
    public function requestMerchantAppOnboardingPostReturnsHttpResponseCode412WhenThereIsAnErrorOnThePlatformImplementationConfiguration(
        AppMerchantBackendApiTester $I
    ): void {
        // Arrange
        $merchantReference = Uuid::uuid4()->toString();
        $tenantIdentifier = Uuid::uuid4()->toString();

        $I->haveAppConfigForTenant($tenantIdentifier);
        $expectedMessage = 'Platform not configured properly to onboard merchants';

        $merchantAppOnboardingResponseTransfer = new MerchantAppOnboardingResponseTransfer();
        $merchantAppOnboardingResponseTransfer
            ->setIsSuccessful(false)
            ->setMessage($expectedMessage);

        $I->mockPlatformPluginImplementationWithMerchantAppOnboardingResponseTransfer($merchantAppOnboardingResponseTransfer);

        $url = $I->buildMerchantAppOnboardingUrl();

        $I->addHeader('x-tenant-identifier', $tenantIdentifier);
        $I->addHeader('x-merchant-reference', $merchantReference);
        $I->addHeader('content-type', 'application/json');

        // Act
        $I->sendPost($url, ['merchant' => [], 'successUrl' => 'successUrl', 'errorUrl' => 'errorUrl', 'cancelUrl' => 'cancelUrl']);

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_PRECONDITION_FAILED);
        $I->seeResponseIsJson();
        $I->seeResponseJsonContainsError($expectedMessage);

        // Cleanup
        $I->addCleanup(function () use ($tenantIdentifier, $merchantReference): void {
            SpyMerchantQuery::create()
                ->filterByTenantIdentifier($tenantIdentifier)
                ->filterByMerchantReference($merchantReference)
                ->delete();
        });
    }
}
