<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\PaymentTransmissions;

use ArrayObject;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\PaymentTransmissionsRequestTransfer;
use Spryker\Zed\AppMerchant\Business\Message\MessageBuilder;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantRepositoryInterface;

class PaymentTransmissionsRequestExtender
{
    public function __construct(protected AppMerchantRepositoryInterface $appMerchantRepository)
    {
    }

    public function extendPaymentsTransmissionsRequest(
        PaymentTransmissionsRequestTransfer $paymentTransmissionsRequestTransfer
    ): PaymentTransmissionsRequestTransfer {
        $merchantReferences = [];
        $orderItemsWithMerchants = [];

        $clonedPaymentsTransmissionsRequestTransfer = clone $paymentTransmissionsRequestTransfer;
        $clonedPaymentsTransmissionsRequestTransfer->setPaymentsTransmissions(new ArrayObject());

        foreach ($paymentTransmissionsRequestTransfer->getPaymentsTransmissions() as $paymentsTransmission) {
            foreach ($paymentsTransmission->getOrderItems() as $orderItemTransfer) {
                if (!$orderItemTransfer->getMerchantReference()) {
                    continue;
                }

                if (!isset($orderItemsWithMerchants[$orderItemTransfer->getOrderReference()])) {
                    $orderItemsWithMerchants[$orderItemTransfer->getOrderReference()] = [];
                }

                if (!isset($orderItemsWithMerchants[$orderItemTransfer->getOrderReference()][$orderItemTransfer->getMerchantReference()])) {
                    $orderItemsWithMerchants[$orderItemTransfer->getOrderReference()][$orderItemTransfer->getMerchantReference()] = [
                        'orderItems' => [],
                        'paymentTransmission' => clone $paymentsTransmission,
                    ];
                }

                $orderItemsWithMerchants[$orderItemTransfer->getOrderReference()][$orderItemTransfer->getMerchantReference()]['orderItems'][] = $orderItemTransfer;
                $merchantReferences[$orderItemTransfer->getMerchantReference()] = $orderItemTransfer->getMerchantReference();
            }
        }

        return $this->addPaymentTransmissionsForOrderItemsWithMerchants(
            $clonedPaymentsTransmissionsRequestTransfer,
            $orderItemsWithMerchants,
            $merchantReferences,
        );
    }

    /**
     * First array key is the order reference, second array key is the merchant reference.
     *
     * @param array<string, array<string, array{orderItems: array<\Generated\Shared\Transfer\OrderItemTransfer>, paymentTransmission: \Generated\Shared\Transfer\PaymentTransmissionTransfer}>> $orderItemsWithMerchants
     * @param array<string> $merchantReferences
     */
    protected function addPaymentTransmissionsForOrderItemsWithMerchants(
        PaymentTransmissionsRequestTransfer $paymentTransmissionsRequestTransfer,
        array $orderItemsWithMerchants,
        array $merchantReferences
    ): PaymentTransmissionsRequestTransfer {
        if ($orderItemsWithMerchants === []) {
            return $paymentTransmissionsRequestTransfer;
        }

        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer
            ->setMerchantReferences($merchantReferences)
            ->setTenantIdentifier($paymentTransmissionsRequestTransfer->getTenantIdentifierOrFail());

        $merchantTransfers = $this->appMerchantRepository->findMerchants($merchantCriteriaTransfer);

        foreach ($orderItemsWithMerchants as $orderItemWithMerchant) {
            foreach ($orderItemWithMerchant as $merchantReference => $merchantData) {
                $merchantTransfer = $this->findMerchantForOrderItem($merchantTransfers, $merchantReference);

                if (!$merchantTransfer instanceof MerchantTransfer) {
                    $paymentTransmissionTransfer = $merchantData['paymentTransmission'];
                    $paymentTransmissionTransfer
                        ->setOrderItems(new ArrayObject($merchantData['orderItems']))
                        ->setIsSuccessful(false)
                        ->setMessage(MessageBuilder::merchantByReferenceNotFound($merchantReference));

                    $paymentTransmissionsRequestTransfer->addFailedPaymentTransmission($paymentTransmissionTransfer);

                    continue;
                }

                $paymentTransmissionTransfer = $merchantData['paymentTransmission'];
                $paymentTransmissionTransfer
                    ->setOrderItems(new ArrayObject($merchantData['orderItems']))
                    ->setMerchant($this->findMerchantForOrderItem($merchantTransfers, $merchantReference))
                    ->setMerchantReference($merchantReference);

                $paymentTransmissionsRequestTransfer->addPaymentTransmission($paymentTransmissionTransfer);
            }
        }

        return $paymentTransmissionsRequestTransfer;
    }

    /**
     * @param array<\Generated\Shared\Transfer\MerchantTransfer> $merchantTransfers
     */
    protected function findMerchantForOrderItem(array $merchantTransfers, string $merchantReference): ?MerchantTransfer
    {
        foreach ($merchantTransfers as $merchantTransfer) {
            if ($merchantReference === $merchantTransfer->getMerchantReferenceOrFail()) {
                return $merchantTransfer;
            }
        }

        return null;
    }
}
