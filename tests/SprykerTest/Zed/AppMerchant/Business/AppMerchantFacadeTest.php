<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Zed\AppMerchant\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AppConfigTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Ramsey\Uuid\Uuid;
use SprykerTest\Zed\AppMerchant\AppMerchantBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group AppMerchant
 * @group Business
 * @group Facade
 * @group AppMerchantFacadeTest
 * Add your own group annotations below this line
 */
class AppMerchantFacadeTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\AppMerchant\AppMerchantBusinessTester
     */
    protected AppMerchantBusinessTester $tester;

    /**
     * @return void
     */
    public function testFindMerchantReturnsMerchantTransferWhenMerchantWasFound(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchantPersisted();

        $merchantAppOnboardingRequestTransfer = (new MerchantCriteriaTransfer())
            ->setMerchantReference($merchantTransfer->getMerchantReference())
            ->setTenantIdentifier($merchantTransfer->getTenantIdentifier());

        // Act
        $merchantTransfer = $this->tester->getFacade()->findMerchant($merchantAppOnboardingRequestTransfer);

        // Assert
        $this->assertInstanceOf(MerchantTransfer::class, $merchantTransfer);
    }

    /**
     * @return void
     */
    public function testFindMerchantReturnsNullWhenMerchantWasNotFound(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        $merchantAppOnboardingRequestTransfer = (new MerchantCriteriaTransfer())
            ->setMerchantReference($merchantTransfer->getMerchantReference())
            ->setTenantIdentifier($merchantTransfer->getTenantIdentifier());

        // Act
        $merchantTransfer = $this->tester->getFacade()->findMerchant($merchantAppOnboardingRequestTransfer);

        // Assert
        $this->tester->assertNull($merchantTransfer);
    }

    /**
     * @return void
     */
    public function testDeleteMerchantByAppConfigRemovesAllTheMerchantsForTenantIdentifierSpecifiedInAppConfig(): void
    {
        // Arrange
        $tenantIdentifierToDelete = Uuid::uuid4()->toString();
        $tenantIdentifierToCheck = Uuid::uuid4()->toString();

        $merchantTransferToDelete = $this->tester->haveMerchantPersisted([
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifierToDelete,
        ]);
        $merchantTransferToCheck = $this->tester->haveMerchantPersisted([
            MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifierToCheck,
        ]);

        // Act
        $this->tester->getFacade()->deleteMerchantByAppConfig(
            (new AppConfigTransfer())->setTenantIdentifier($tenantIdentifierToDelete),
        );
        $foundMerchantTransferToDelete = $this->tester->getFacade()->findMerchant(
            (new MerchantCriteriaTransfer())->setTenantIdentifier($tenantIdentifierToDelete),
        );
        $foundMerchantTransferToCheck = $this->tester->getFacade()->findMerchant(
            (new MerchantCriteriaTransfer())->setTenantIdentifier($tenantIdentifierToCheck),
        );

        // Assert
        $this->tester->assertNull($foundMerchantTransferToDelete);
        $this->tester->assertNotNull($foundMerchantTransferToCheck);
        $this->tester->assertSame($merchantTransferToCheck->getMerchantReference(), $foundMerchantTransferToCheck->getMerchantReference());
    }
}
