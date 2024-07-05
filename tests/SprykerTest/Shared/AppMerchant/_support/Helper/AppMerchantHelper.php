<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Shared\AppMerchant\Helper;

use Codeception\Module;
use Generated\Shared\DataBuilder\MerchantAppOnboardingDetailsBuilder;
use Generated\Shared\DataBuilder\MerchantAppOnboardingStatusChangedBuilder;
use Generated\Shared\DataBuilder\MerchantBuilder;
use Generated\Shared\DataBuilder\ReadyForMerchantAppOnboardingBuilder;
use Generated\Shared\Transfer\AppConfigTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingDetailsTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingRequestTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingStatusChangedTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\OnboardingTransfer;
use Generated\Shared\Transfer\ReadyForMerchantAppOnboardingTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Orm\Zed\AppMerchant\Persistence\SpyMerchant;
use Spryker\Zed\AppMerchant\AppMerchantConfig;
use Spryker\Zed\AppMerchant\AppMerchantDependencyProvider;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantRepository;
use Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Shared\Testify\Helper\DependencyHelperTrait;

class AppMerchantHelper extends Module
{
    use DependencyHelperTrait;
    use DataCleanupHelperTrait;

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\ReadyForMerchantAppOnboardingTransfer
     */
    public function haveReadyForMerchantAppOnboardingTransfer(array $seed = []): ReadyForMerchantAppOnboardingTransfer
    {
        return (new ReadyForMerchantAppOnboardingBuilder($seed))->withOnboarding()->build();
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\MerchantAppOnboardingStatusChangedTransfer
     */
    public function haveMerchantAppOnboardingStatusChangedTransfer(array $seed = []): MerchantAppOnboardingStatusChangedTransfer
    {
        return (new MerchantAppOnboardingStatusChangedBuilder($seed))->build();
    }

    public function haveMerchantAppOnboardingDetailsPersisted(array $seed = []): MerchantAppOnboardingDetailsTransfer
    {
        $merchantAppOnboardingDetailsTransfer = $this->haveMerchantAppOnboardingDetails($seed);
        $merchantAppOnboardingDetailsEntity = new SpyMerchantAppOnboardingDetails();
        $merchantAppOnboardingDetailsEntity->fromArray($merchantAppOnboardingDetailsTransfer->toArray());
        $merchantAppOnboardingDetailsEntity->setDetails(json_encode($merchantAppOnboardingDetailsTransfer->toArray()));

        $merchantAppOnboardingDetailsEntity->save();

        return $merchantAppOnboardingDetailsTransfer;
    }

    public function haveMerchantAppOnboardingDetails(array $seed = []): MerchantAppOnboardingDetailsTransfer
    {
        return (new MerchantAppOnboardingDetailsBuilder($seed))->withOnboarding($seed)->build();
    }

    public function assertOnboardingEquals(OnboardingTransfer $onboardingTransfer, string $tenantIdentifier): void
    {
        $appMerchantRepository = new AppMerchantRepository();
        $merchantAppOnboardingCriteriaTransfer = (new MerchantAppOnboardingCriteriaTransfer())->setTenantIdentifier($tenantIdentifier);
        $merchantAppOnboardingDetailsTransfer = $appMerchantRepository->findOneByCriteria($merchantAppOnboardingCriteriaTransfer);

        $this->assertEquals($onboardingTransfer, $merchantAppOnboardingDetailsTransfer->getOnboarding());
    }

    public function mockPlatformPluginImplementation(
        ?MerchantAppOnboardingDetailsTransfer $merchantAppOnboardingDetailsTransfer = null,
        ?MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer = null
    ): void {
        $merchantAppOnboardingDetailsTransfer ??= (new MerchantAppOnboardingDetailsTransfer())
            ->setTenantIdentifier('tenant identifier')
            ->setAppName('AppName')
            ->setType('Type')
            ->setOnboarding((new OnboardingTransfer())
                ->setStrategy('redirect')
                ->setUrl('https://example.com/onboarding'));

        $merchantAppOnboardingResponseTransfer ??= new MerchantAppOnboardingResponseTransfer();

        $this->setDependency(AppMerchantDependencyProvider::PLUGIN_PLATFORM, new class ($merchantAppOnboardingDetailsTransfer, $merchantAppOnboardingResponseTransfer) implements AppMerchantPlatformPluginInterface {
            public function __construct(
                protected ?MerchantAppOnboardingDetailsTransfer $merchantAppOnboardingDetailsTransfer = null,
                protected ?MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer = null
            ) {
            }

            public function provideOnboardingDetails(
                AppConfigTransfer $appConfigTransfer,
                MerchantAppOnboardingDetailsTransfer $merchantAppOnboardingDetailsTransfer
            ): MerchantAppOnboardingDetailsTransfer {
                return $this->merchantAppOnboardingDetailsTransfer ?? new MerchantAppOnboardingDetailsTransfer();
            }

            public function onboardMerchant(
                MerchantAppOnboardingRequestTransfer $merchantAppOnboardingRequestTransfer,
                MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer
            ): MerchantAppOnboardingResponseTransfer {
                $merchantAppOnboardingResponseTransfer = $this->merchantAppOnboardingResponseTransfer ?? new MerchantAppOnboardingResponseTransfer();

                if (!($merchantAppOnboardingResponseTransfer->getMerchant() instanceof MerchantTransfer)) {
                    $merchantAppOnboardingResponseTransfer->setMerchant($merchantAppOnboardingRequestTransfer->getMerchant());
                }

                return $merchantAppOnboardingResponseTransfer;
            }

            public function handleWebhook(
                WebhookRequestTransfer $webhookRequestTransfer,
                WebhookResponseTransfer $webhookResponseTransfer
            ): WebhookResponseTransfer {
                return $webhookResponseTransfer;
            }
        });
    }

    public function mockPlatformPluginImplementationWithMerchantAppOnboardingResponseTransfer(
        MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer
    ): void {
        $this->mockPlatformPluginImplementation(new MerchantAppOnboardingDetailsTransfer(), $merchantAppOnboardingResponseTransfer);
    }

    public function mockPlatformPluginImplementationWithMerchantAppOnboardingDetailsTransfer(
        MerchantAppOnboardingDetailsTransfer $merchantAppOnboardingDetailsTransfer
    ): void {
        $this->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer, new MerchantAppOnboardingResponseTransfer());
    }

    public function haveMerchantPersisted(array $seed = []): MerchantTransfer
    {
        $merchantTransfer = $this->haveMerchant($seed);

        $merchant = $merchantTransfer->toArray();
        $merchant[MerchantTransfer::CONFIG] = json_encode($merchant[MerchantTransfer::CONFIG]);

        $merchantEntity = new SpyMerchant();
        $merchantEntity->fromArray($merchant);

        $merchantEntity->save();

        $this->getDataCleanupHelper()->addCleanup(function () use ($merchantEntity): void {
            $merchantEntity->delete();
        });

        return $merchantTransfer;
    }

    public function haveMerchant(array $seed = []): MerchantTransfer
    {
        $seed[MerchantTransfer::CONFIG] = $seed[MerchantTransfer::CONFIG] ?? [];

        return (new MerchantBuilder($seed))->build();
    }

    public function seeMerchantAppOnboardingStatus(MerchantTransfer $expectedMerchantTransfer): void
    {
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer
            ->setMerchantReference($expectedMerchantTransfer->getMerchantReferenceOrFail())
            ->setTenantIdentifier($expectedMerchantTransfer->getTenantIdentifierOrFail());

        $appMerchantRepository = new AppMerchantRepository();
        $merchantTransfer = $appMerchantRepository->findMerchant($merchantCriteriaTransfer);

        $this->assertNotNull($merchantTransfer, 'Expected to have a Merchant persisted but was not.');
        $this->assertSame($expectedMerchantTransfer->getConfig()[AppMerchantConfig::MERCHANT_ONBOARDING_STATUS], $merchantTransfer->getConfig()[AppMerchantConfig::MERCHANT_ONBOARDING_STATUS]);
        $this->assertSame($expectedMerchantTransfer->getTenantIdentifier(), $merchantTransfer->getTenantIdentifier());
    }
}
