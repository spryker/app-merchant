<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Zed\AppMerchant\Communication\Plugin\AppPayment;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\OrderItemTransfer;
use Generated\Shared\Transfer\PaymentTransmissionsRequestTransfer;
use Generated\Shared\Transfer\PaymentTransmissionTransfer;
use Orm\Zed\AppPayment\Persistence\SpyPaymentQuery;
use Orm\Zed\AppPayment\Persistence\SpyPaymentTransferQuery;
use Ramsey\Uuid\Uuid;
use Spryker\Zed\AppMerchant\Business\Message\MessageBuilder;
use Spryker\Zed\AppMerchant\Communication\Plugin\AppPayment\MerchantsPaymentsTransmissionsRequestExtenderPlugin;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Zed\AppMerchant\AppMerchantCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group AppMerchant
 * @group Communication
 * @group Plugin
 * @group AppPayment
 * @group PaymentsTransmissionsRequestExpanderPluginTest
 * Add your own group annotations below this line
 */
class PaymentsTransmissionsRequestExpanderPluginTest extends Unit
{
    use DataCleanupHelperTrait;

    protected AppMerchantCommunicationTester $tester;

    public function testGivenTwoOrdersEachWithThreeItemsWhereOneOfThemIsFromAMerchantWhenTheExtenderRunsThenTwoPaymentTransmissionsAreAddedTwoItemsAndTwoWithMerchantsAndEachHasTwoItemsOrderItemsWithoutMerchantsAreIgnored(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $orderReference1 = Uuid::uuid4()->toString();
        $orderReference2 = Uuid::uuid4()->toString();

        $merchantTransfer1 = $this->tester->haveMerchantPersisted([MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier]);
        $merchantTransfer2 = $this->tester->haveMerchantPersisted([MerchantTransfer::TENANT_IDENTIFIER => $tenantIdentifier]);

        /** @var array<\Generated\Shared\Transfer\OrderItemTransfer> $orderItems */
        $orderItems = [
            $this->tester->haveOrderItem([OrderItemTransfer::ORDER_REFERENCE => $orderReference1]),
            $this->tester->haveOrderItem([OrderItemTransfer::ORDER_REFERENCE => $orderReference1, OrderItemTransfer::MERCHANT_REFERENCE => $merchantTransfer1->getMerchantReference()]),
            $this->tester->haveOrderItem([OrderItemTransfer::ORDER_REFERENCE => $orderReference2, OrderItemTransfer::MERCHANT_REFERENCE => $merchantTransfer2->getMerchantReference()]),
            $this->tester->haveOrderItem([OrderItemTransfer::ORDER_REFERENCE => $orderReference1]),
            $this->tester->haveOrderItem([OrderItemTransfer::ORDER_REFERENCE => $orderReference1, OrderItemTransfer::MERCHANT_REFERENCE => $merchantTransfer1->getMerchantReference()]),
            $this->tester->haveOrderItem([OrderItemTransfer::ORDER_REFERENCE => $orderReference2, OrderItemTransfer::MERCHANT_REFERENCE => $merchantTransfer2->getMerchantReference()]),
        ];

        $paymentTransmissionTransfer = new PaymentTransmissionTransfer();
        $paymentTransmissionTransfer->setOrderItems(new ArrayObject($orderItems));

        $paymentTransmissionsRequestTransfer = new PaymentTransmissionsRequestTransfer();
        $paymentTransmissionsRequestTransfer
            ->setTenantIdentifier($tenantIdentifier)
            ->addPaymentTransmission($paymentTransmissionTransfer);

        // Act
        $paymentTransmissionRequestExtenderPlugin = new MerchantsPaymentsTransmissionsRequestExtenderPlugin();
        $paymentTransmissionsRequestTransfer = $paymentTransmissionRequestExtenderPlugin->extendPaymentTransmissionsRequest($paymentTransmissionsRequestTransfer);

        // Assert
        $this->assertCount(2, $paymentTransmissionsRequestTransfer->getPaymentsTransmissions());

        // First PaymentTransmission has Merchant 1
        $this->tester->assertPaymentTransmissionEquals(
            /** @phpstan-var \Generated\Shared\Transfer\PaymentTransmissionTransfer */
            $paymentTransmissionsRequestTransfer->getPaymentsTransmissions()[0],
            [$orderItems[1], $orderItems[4]],
            $merchantTransfer1->getMerchantReference(),
        );

        // Second PaymentTransmission has Merchant 2
        $this->tester->assertPaymentTransmissionEquals(
            /** @phpstan-var \Generated\Shared\Transfer\PaymentTransmissionTransfer */
            $paymentTransmissionsRequestTransfer->getPaymentsTransmissions()[1],
            [$orderItems[2], $orderItems[5]],
            $merchantTransfer2->getMerchantReference(),
        );

        $this->getDataCleanupHelper()->_addCleanup(function () use ($tenantIdentifier): void {
            SpyPaymentQuery::create()->filterByTenantIdentifier($tenantIdentifier)->delete();
            SpyPaymentTransferQuery::create()->filterByTenantIdentifier($tenantIdentifier)->delete();
        });
    }

    public function testGivenOrderItemWithoutMerchantWhenTheExtenderRunsThenNoPaymentTransmissionsIsAdded(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $orderReference = Uuid::uuid4()->toString();

        /** @var array<\Generated\Shared\Transfer\OrderItemTransfer> $orderItems */
        $orderItems = [
            $this->tester->haveOrderItem([OrderItemTransfer::ORDER_REFERENCE => $orderReference]),
            $this->tester->haveOrderItem([OrderItemTransfer::ORDER_REFERENCE => $orderReference]),
        ];

        $paymentTransmissionTransfer = new PaymentTransmissionTransfer();
        $paymentTransmissionTransfer->setOrderItems(new ArrayObject($orderItems));

        $paymentTransmissionsRequestTransfer = new PaymentTransmissionsRequestTransfer();
        $paymentTransmissionsRequestTransfer
            ->setTenantIdentifier($tenantIdentifier)
            ->addPaymentTransmission($paymentTransmissionTransfer);

        // Act
        $paymentTransmissionRequestExtenderPlugin = new MerchantsPaymentsTransmissionsRequestExtenderPlugin();
        $paymentTransmissionsRequestTransfer = $paymentTransmissionRequestExtenderPlugin->extendPaymentTransmissionsRequest($paymentTransmissionsRequestTransfer);

        // Assert
        $this->assertCount(0, $paymentTransmissionsRequestTransfer->getPaymentsTransmissions());
    }

    public function testGivenOrderItemWithAMerchantReferenceThatDoesNotExistsWhenTheExtenderRunsThenAnExceptionIsThrown(): void
    {
        // Arrange
        $tenantIdentifier = Uuid::uuid4()->toString();
        $orderReference = Uuid::uuid4()->toString();

        /** @var array<\Generated\Shared\Transfer\OrderItemTransfer> $orderItems */
        $orderItems = [
            $this->tester->haveOrderItem([OrderItemTransfer::ORDER_REFERENCE => $orderReference, OrderItemTransfer::MERCHANT_REFERENCE => 'non-existing-merchant-reference']),
        ];

        $paymentTransmissionTransfer = new PaymentTransmissionTransfer();
        $paymentTransmissionTransfer->setOrderItems(new ArrayObject($orderItems));

        $paymentTransmissionsRequestTransfer = new PaymentTransmissionsRequestTransfer();
        $paymentTransmissionsRequestTransfer
            ->setTenantIdentifier($tenantIdentifier)
            ->addPaymentTransmission($paymentTransmissionTransfer);

        // Act
        $paymentTransmissionRequestExtenderPlugin = new MerchantsPaymentsTransmissionsRequestExtenderPlugin();
        $paymentTransmissionsResponseTransfer = $paymentTransmissionRequestExtenderPlugin->extendPaymentTransmissionsRequest($paymentTransmissionsRequestTransfer);

        $this->assertCount(1, $paymentTransmissionsResponseTransfer->getFailedPaymentTransmissions());

        /** @var \Generated\Shared\Transfer\PaymentTransmissionTransfer $paymentTransmissionTransfer */
        $paymentTransmissionTransfer = $paymentTransmissionsResponseTransfer->getFailedPaymentTransmissions()[0];

        $this->assertFalse($paymentTransmissionTransfer->getIsSuccessful());
        $this->assertSame(MessageBuilder::merchantByReferenceNotFound('non-existing-merchant-reference'), $paymentTransmissionTransfer->getMessage());
    }
}
