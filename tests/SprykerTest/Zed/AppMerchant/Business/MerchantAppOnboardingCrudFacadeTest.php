<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Zed\AppMerchant\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantAppOnboardingRequestTransfer;
use SprykerTest\Zed\AppMerchant\AppMerchantBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group AppMerchant
 * @group Business
 * @group Facade
 * @group MerchantAppOnboardingCrudFacadeTest
 * Add your own group annotations below this line
 */
class MerchantAppOnboardingCrudFacadeTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\AppMerchant\AppMerchantBusinessTester
     */
    protected AppMerchantBusinessTester $tester;

    /**
     * @return void
     */
    public function testCreateMerchantAppOnboardingReturnsAMerchantEntityWhenEntityWasSaved(): void
    {
        // Arrange
        $this->tester->mockPlatformPluginImplementation();
        $merchantTransfer = $this->tester->haveMerchant();

        $this->tester->haveAppConfigForTenant($merchantTransfer->getTenantIdentifierOrFail());

        $merchantAppOnboardingRequestTransfer = (new MerchantAppOnboardingRequestTransfer())
            ->setTenantIdentifier($merchantTransfer->getTenantIdentifier())
            ->setMerchant($merchantTransfer);

        // Act
        $merchantAppOnboardingResponseTransfer = $this->tester->getFacade()->createMerchantAppOnboarding($merchantAppOnboardingRequestTransfer);

        // Assert
        $this->tester->assertMerchantAppOnboardingResponseContainsMerchantTransfer($merchantAppOnboardingResponseTransfer, $merchantTransfer);
    }
}
