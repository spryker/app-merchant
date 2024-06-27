<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\AsyncApi\AppMerchant\AppMerchantTests\AppEvents;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantAppOnboardingDetailsTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;
use Generated\Shared\Transfer\OnboardingTransfer;
use Spryker\Zed\AppKernel\AppKernelDependencyProvider;
use Spryker\Zed\AppKernel\Business\AppKernelFacade;
use Spryker\Zed\AppMerchant\Communication\Plugin\AppKernel\InformTenantAboutMerchantAppOnboardingReadinessConfigurationAfterSavePlugin;
use SprykerTest\AsyncApi\AppMerchant\AppMerchantAsyncApiTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group AsyncApi
 * @group AppMerchant
 * @group AppMerchantTests
 * @group AppEvents
 * @group ReadyForMerchantAppOnboardingTest
 * Add your own group annotations below this line
 */
class ReadyForMerchantAppOnboardingTest extends Unit
{
    /**
     * @var \SprykerTest\AsyncApi\AppMerchant\AppMerchantAsyncApiTester
     */
    protected AppMerchantAsyncApiTester $tester;

    /**
     * @return void
     */
    public function testGivenTenantIsNotConnectedWithAppWhenMarketplaceOwnerConfiguresTheAppThenTheReadyForMerchantAppOnboardingMessageIsSend(): void
    {
        // Arrange
        $readyForMerchantAppOnboardingTransfer = $this->tester->haveReadyForMerchantAppOnboardingTransfer();
        $appConfigTransfer = $this->tester->haveAppConfigTransfer();

        $onboardingTransfer = new OnboardingTransfer();
        $onboardingTransfer
            ->setStrategy('test-strategy')
            ->setUrl('url');

        $merchantAppOnboardingDetailsTransfer = new MerchantAppOnboardingDetailsTransfer();
        $merchantAppOnboardingDetailsTransfer
            ->setType('TestType')
            ->setAppName('AppName')
            ->setAppIdentifier('AppIdentifier')
            ->setOnboarding($onboardingTransfer);

        $this->tester->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer, new MerchantAppOnboardingResponseTransfer());
        $this->tester->setDependency(AppKernelDependencyProvider::PLUGIN_CONFIGURATION_BEFORE_SAVE_PLUGINS, []);
        $this->tester->setDependency(AppKernelDependencyProvider::PLUGIN_CONFIGURATION_AFTER_SAVE_PLUGINS, [
            new InformTenantAboutMerchantAppOnboardingReadinessConfigurationAfterSavePlugin(),
        ]);

        // Act
        $appKernelFacade = new AppKernelFacade();
        $appKernelFacade->saveConfig($appConfigTransfer);

        // Assert
        $this->tester->assertMessageWasEmittedOnChannel($readyForMerchantAppOnboardingTransfer, 'merchant-app-events');
    }

    /**
     * @return void
     */
    public function testGivenTenantIsConnectedWithAppAndTheOnboardingDetailsWereAlreadySentWhenMarketplaceOwnerUpdatesTheAppConfigurationThenTheReadyForMerchantAppOnboardingMessageIsSendAgain(): void
    {
        // Arrange
        $appConfigTransfer = $this->tester->haveAppConfigTransfer(['config' => ['tenant-onboarding-status' => 'onboarded']]); // Ensure even with this configuration the message is sent.
        $readyForMerchantAppOnboardingTransfer = $this->tester->haveReadyForMerchantAppOnboardingTransfer();

        $onboardingTransfer = new OnboardingTransfer();
        $onboardingTransfer
            ->setStrategy('test-strategy')
            ->setUrl('url');

        $merchantAppOnboardingDetailsTransfer = new MerchantAppOnboardingDetailsTransfer();
        $merchantAppOnboardingDetailsTransfer
            ->setType('TestType')
            ->setAppName('AppName')
            ->setAppIdentifier('AppIdentifier')
            ->setOnboarding($onboardingTransfer);

        $this->tester->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer, new MerchantAppOnboardingResponseTransfer());
        $this->tester->setDependency(AppKernelDependencyProvider::PLUGIN_CONFIGURATION_BEFORE_SAVE_PLUGINS, []);
        $this->tester->setDependency(AppKernelDependencyProvider::PLUGIN_CONFIGURATION_AFTER_SAVE_PLUGINS, [
            new InformTenantAboutMerchantAppOnboardingReadinessConfigurationAfterSavePlugin(),
        ]);

        // Act

        $appKernelFacade = new AppKernelFacade();
        $appKernelFacade->saveConfig($appConfigTransfer);

        // Assert
        $this->tester->assertMessageWasEmittedOnChannel($readyForMerchantAppOnboardingTransfer, 'merchant-app-events');
    }

    /**
     * @return void
     */
    public function testGivenTenantIsNotConnectedWithAppWhenMarketplaceOwnerConfiguresTheAppThenTheReadyForMerchantAppOnboardingMessageIsSendWithTheStrategyAndUrlProvidedByTheAppPluginImplementation(): void
    {
        // Arrange
        $expectedStrategy = 'test-strategy';
        $expectedUrl = 'www.test-url.com';

        $readyForMerchantAppOnboardingTransfer = $this->tester->haveReadyForMerchantAppOnboardingTransfer();
        $appConfigTransfer = $this->tester->haveAppConfigTransfer();

        $merchantAppOnboardingDetailsTransfer = new MerchantAppOnboardingDetailsTransfer();
        $merchantAppOnboardingDetailsTransfer
            ->setType('TestType')
            ->setAppName('AppName')
            ->setAppIdentifier('AppIdentifier');

        $onboardingTransfer = new OnboardingTransfer();
        $onboardingTransfer->setStrategy($expectedStrategy);
        $onboardingTransfer->setUrl($expectedUrl);

        $merchantAppOnboardingDetailsTransfer->setOnboarding($onboardingTransfer);

        $this->tester->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer);

        $this->tester->setDependency(AppKernelDependencyProvider::PLUGIN_CONFIGURATION_BEFORE_SAVE_PLUGINS, []);
        $this->tester->setDependency(AppKernelDependencyProvider::PLUGIN_CONFIGURATION_AFTER_SAVE_PLUGINS, [
            new InformTenantAboutMerchantAppOnboardingReadinessConfigurationAfterSavePlugin(),
        ]);

        // Act
        $appKernelFacade = new AppKernelFacade();
        $appKernelFacade->saveConfig($appConfigTransfer);

        // Assert
        $this->tester->assertMessageWasEmittedOnChannel($readyForMerchantAppOnboardingTransfer, 'merchant-app-events');
    }

    /**
     * @return void
     */
    public function testGivenTenantIsNotConnectedWithAppWhenThePlatformPluginImplementationReturnsTheApiStrategyAndNoUrlThenTheApiEndpointUrlIsUsedFromTheAppMerchant(): void
    {
        // Arrange
        $expectedStrategy = 'api';

        $readyForMerchantAppOnboardingTransfer = $this->tester->haveReadyForMerchantAppOnboardingTransfer();
        $appConfigTransfer = $this->tester->haveAppConfigTransfer();

        $merchantAppOnboardingDetailsTransfer = new MerchantAppOnboardingDetailsTransfer();
        $merchantAppOnboardingDetailsTransfer
            ->setType('TestType')
            ->setAppName('AppName')
            ->setAppIdentifier('AppIdentifier');

        $onboardingTransfer = new OnboardingTransfer();
        $onboardingTransfer->setStrategy($expectedStrategy);

        $merchantAppOnboardingDetailsTransfer->setOnboarding($onboardingTransfer);

        $this->tester->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer);

        $this->tester->setDependency(AppKernelDependencyProvider::PLUGIN_CONFIGURATION_BEFORE_SAVE_PLUGINS, []);
        $this->tester->setDependency(AppKernelDependencyProvider::PLUGIN_CONFIGURATION_AFTER_SAVE_PLUGINS, [
            new InformTenantAboutMerchantAppOnboardingReadinessConfigurationAfterSavePlugin(),
        ]);

        // Act
        $appKernelFacade = new AppKernelFacade();
        $appKernelFacade->saveConfig($appConfigTransfer);

        // Assert
        $this->tester->assertMessageWasEmittedOnChannel($readyForMerchantAppOnboardingTransfer, 'merchant-app-events');
    }
}
