<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\PaymentsTransmissions;

use ArrayObject;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\PaymentsTransmissionsRequestTransfer;
use Spryker\Zed\AppMerchant\Business\Message\MessageBuilder;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantRepositoryInterface;

class PaymentsTransmissionsRequestExtender
{
    public function __construct(protected AppMerchantRepositoryInterface $appMerchantRepository)
    {
    }

    public function extendPaymentsTransmissionsRequest(
        PaymentsTransmissionsRequestTransfer $paymentsTransmissionsRequestTransfer
    ): PaymentsTransmissionsRequestTransfer {
        $merchantReferences = [];
        $orderItemsWithMerchants = [];

        $clonedPaymentsTransmissionsRequestTransfer = clone $paymentsTransmissionsRequestTransfer;
        $clonedPaymentsTransmissionsRequestTransfer->setPaymentsTransmissions(new ArrayObject());

        foreach ($paymentsTransmissionsRequestTransfer->getPaymentsTransmissions() as $paymentsTransmission) {
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

        return $this->addPaymentsTransmissionsForOrderItemsWithMerchants(
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
    protected function addPaymentsTransmissionsForOrderItemsWithMerchants(
        PaymentsTransmissionsRequestTransfer $paymentsTransmissionsRequestTransfer,
        array $orderItemsWithMerchants,
        array $merchantReferences
    ): PaymentsTransmissionsRequestTransfer {
        if ($orderItemsWithMerchants === []) {
            return $paymentsTransmissionsRequestTransfer;
        }

        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer
            ->setMerchantReferences($merchantReferences)
            ->setTenantIdentifier($paymentsTransmissionsRequestTransfer->getTenantIdentifierOrFail());

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

                    $paymentsTransmissionsRequestTransfer->addFailedPaymentTransmission($paymentTransmissionTransfer);

                    continue;
                }

                $paymentTransmissionTransfer = $merchantData['paymentTransmission'];
                $paymentTransmissionTransfer
                    ->setOrderItems(new ArrayObject($merchantData['orderItems']))
                    ->setMerchant($this->findMerchantForOrderItem($merchantTransfers, $merchantReference))
                    ->setMerchantReference($merchantReference);

                $paymentsTransmissionsRequestTransfer->addPaymentTransmission($paymentTransmissionTransfer);
            }
        }

        return $paymentsTransmissionsRequestTransfer;
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
