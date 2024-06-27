<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Zed\AppMerchant\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantAppOnboardingDetailsTransfer;
use Generated\Shared\Transfer\OnboardingTransfer;
use Spryker\Zed\AppMerchant\Business\Exception\AppMerchantAppOnboardingConfigurationException;
use Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding\MerchantAppOnboardingStrategy;
use Spryker\Zed\AppMerchant\Business\Message\AppMerchantMessage;
use SprykerTest\Zed\AppMerchant\AppMerchantBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group AppMerchant
 * @group Business
 * @group Facade
 * @group AppMerchantFacadeInformTenantAboutMerchantAppOnboardingReadinessTest
 * Add your own group annotations below this line
 */
class AppMerchantFacadeInformTenantAboutMerchantAppOnboardingReadinessTest extends Unit
{
    protected AppMerchantBusinessTester $tester;

    /**
     * @return void
     */
    public function testGivenThePlatformPluginDoesNotAddAnAppNameWhenTheValidationRunsThenAnExceptionIsThrown(): void
    {
        // Arrange
        $appConfigTransfer = $this->tester->haveAppConfigTransfer();
        $merchantAppOnboardingDetailsTransfer = new MerchantAppOnboardingDetailsTransfer();
        $merchantAppOnboardingDetailsTransfer->setOnboarding(new OnboardingTransfer());

        $this->tester->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer);

        // Expect
        $this->expectException(AppMerchantAppOnboardingConfigurationException::class);
        $this->expectExceptionMessage(AppMerchantMessage::appNameNotDefinedExceptionMessage());

        // Act
        $this->tester->getFacade()->informTenantAboutMerchantAppOnboardingReadiness($appConfigTransfer);
    }

    /**
     * @return void
     */
    public function testGivenThePlatformPluginDoesNotAddAnAppIdentifierWhenTheValidationRunsThenAnExceptionIsThrown(): void
    {
        // Arrange
        $appConfigTransfer = $this->tester->haveAppConfigTransfer();
        $merchantAppOnboardingDetailsTransfer = new MerchantAppOnboardingDetailsTransfer();
        $merchantAppOnboardingDetailsTransfer->setOnboarding(new OnboardingTransfer());
        $merchantAppOnboardingDetailsTransfer->setAppName('AppName');

        $this->tester->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer);

        // Expect
        $this->expectException(AppMerchantAppOnboardingConfigurationException::class);
        $this->expectExceptionMessage(AppMerchantMessage::appIdentifierNotDefinedExceptionMessage());

        // Act
        $this->tester->getFacade()->informTenantAboutMerchantAppOnboardingReadiness($appConfigTransfer);
    }

    /**
     * @return void
     */
    public function testGivenThePlatformPluginDoesNotAddATypeWhenTheValidationRunsThenAnExceptionIsThrown(): void
    {
        // Arrange
        $appConfigTransfer = $this->tester->haveAppConfigTransfer();
        $merchantAppOnboardingDetailsTransfer = new MerchantAppOnboardingDetailsTransfer();
        $merchantAppOnboardingDetailsTransfer
            ->setOnboarding(new OnboardingTransfer())
            ->setAppName('AppName')
            ->setAppIdentifier('AppIdentifier');

        $this->tester->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer);

        // Expect
        $this->expectException(AppMerchantAppOnboardingConfigurationException::class);
        $this->expectExceptionMessage(AppMerchantMessage::typeNotDefinedExceptionMessage());

        // Act
        $this->tester->getFacade()->informTenantAboutMerchantAppOnboardingReadiness($appConfigTransfer);
    }

    /**
     * @return void
     */
    public function testGivenThePlatformPluginDoesNotAddAStrategyToTheOnboardingTypeWhenTheValidationRunsThenAnExceptionIsThrown(): void
    {
        // Arrange
        $appConfigTransfer = $this->tester->haveAppConfigTransfer();
        $merchantAppOnboardingDetailsTransfer = new MerchantAppOnboardingDetailsTransfer();
        $merchantAppOnboardingDetailsTransfer
            ->setOnboarding(new OnboardingTransfer())
            ->setType('Type')
            ->setAppName('AppName')
            ->setAppIdentifier('AppIdentifier');

        $this->tester->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer);

        // Expect
        $this->expectException(AppMerchantAppOnboardingConfigurationException::class);
        $this->expectExceptionMessage(AppMerchantMessage::onboardingStrategyNotDefinedExceptionMessage());

        // Act
        $this->tester->getFacade()->informTenantAboutMerchantAppOnboardingReadiness($appConfigTransfer);
    }

    /**
     * @return void
     */
    public function testGivenThePlatformPluginUsesTheIFrameStrategyAndDoesNotAddAUrlToTheOnboardingTypeWhenTheValidationRunsThenAnExceptionIsThrown(): void
    {
        // Arrange
        $appConfigTransfer = $this->tester->haveAppConfigTransfer();

        $onboardingTransfer = new OnboardingTransfer();
        $onboardingTransfer->setStrategy(MerchantAppOnboardingStrategy::IFRAME);

        $merchantAppOnboardingDetailsTransfer = new MerchantAppOnboardingDetailsTransfer();
        $merchantAppOnboardingDetailsTransfer
            ->setOnboarding($onboardingTransfer)
            ->setType('Type')
            ->setAppName('AppName')
            ->setAppIdentifier('AppIdentifier');

        $this->tester->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer);

        // Expect
        $this->expectException(AppMerchantAppOnboardingConfigurationException::class);
        $this->expectExceptionMessage(AppMerchantMessage::onboardingUrlNotDefinedExceptionMessage());

        // Act
        $this->tester->getFacade()->informTenantAboutMerchantAppOnboardingReadiness($appConfigTransfer);
    }

    /**
     * @return void
     */
    public function testGivenThePlatformPluginUsesTheRedirectStrategyAndDoesNotAddAUrlToTheOnboardingTypeWhenTheValidationRunsThenAnExceptionIsThrown(): void
    {
        // Arrange
        $appConfigTransfer = $this->tester->haveAppConfigTransfer();

        $onboardingTransfer = new OnboardingTransfer();
        $onboardingTransfer->setStrategy(MerchantAppOnboardingStrategy::IFRAME);

        $merchantAppOnboardingDetailsTransfer = new MerchantAppOnboardingDetailsTransfer();
        $merchantAppOnboardingDetailsTransfer
            ->setOnboarding($onboardingTransfer)
            ->setType('Type')
            ->setAppName('AppName')
            ->setAppIdentifier('AppIdentifier');

        $this->tester->mockPlatformPluginImplementation($merchantAppOnboardingDetailsTransfer);

        // Expect
        $this->expectException(AppMerchantAppOnboardingConfigurationException::class);
        $this->expectExceptionMessage(AppMerchantMessage::onboardingUrlNotDefinedExceptionMessage());

        // Act
        $this->tester->getFacade()->informTenantAboutMerchantAppOnboardingReadiness($appConfigTransfer);
    }
}
