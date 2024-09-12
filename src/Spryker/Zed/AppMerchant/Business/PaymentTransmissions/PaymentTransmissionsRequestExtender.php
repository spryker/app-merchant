<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\PaymentTransmissions;

use ArrayObject;
use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\PaymentTransmissionsRequestTransfer;
use Generated\Shared\Transfer\PaymentTransmissionTransfer;
use Spryker\Zed\AppMerchant\Business\Message\MessageBuilder;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantRepositoryInterface;

class PaymentTransmissionsRequestExtender
{
    /**
     * @var string
     */
    protected const KEY_PAYMENT_TRANSMISSION = 'paymentTransmission';

    /**
     * @var string
     */
    protected const KEY_PAYMENT_TRANSMISSION_ITEMS = 'paymentTransmissionItems';

    public function __construct(protected AppMerchantRepositoryInterface $appMerchantRepository)
    {
    }

    public function extendPaymentTransmissionsRequest(
        PaymentTransmissionsRequestTransfer $paymentTransmissionsRequestTransfer
    ): PaymentTransmissionsRequestTransfer {
        $clonedPaymentTransmissionsRequestTransfer = clone $paymentTransmissionsRequestTransfer;
        $clonedPaymentTransmissionsRequestTransfer->setPaymentTransmissions(new ArrayObject());

        [$paymentTransmissionItemsGroupedByOrderReferenceAndMerchant, $merchantReferences] = $this->groupPaymentTransmissionItemsByOrderReferenceAndMerchantReference(
            $paymentTransmissionsRequestTransfer,
        );

        return $this->addPaymentTransmissionsForPaymentTransmissionItemsWithMerchants(
            $clonedPaymentTransmissionsRequestTransfer,
            $paymentTransmissionItemsGroupedByOrderReferenceAndMerchant,
            $merchantReferences,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentTransmissionsRequestTransfer $paymentTransmissionsRequestTransfer
     *
     * First array key is the order reference, second array key is the merchant reference.
     * @param array<string, array<string, array<string, list<\Generated\Shared\Transfer\OrderItemTransfer>|\Generated\Shared\Transfer\OrderItemTransfer>>> $paymentTransmissionItemsGroupedByOrderReferenceAndMerchant
     * @param array<string> $merchantReferences
     */
    protected function addPaymentTransmissionsForPaymentTransmissionItemsWithMerchants(
        PaymentTransmissionsRequestTransfer $paymentTransmissionsRequestTransfer,
        array $paymentTransmissionItemsGroupedByOrderReferenceAndMerchant,
        array $merchantReferences
    ): PaymentTransmissionsRequestTransfer {
        if ($paymentTransmissionItemsGroupedByOrderReferenceAndMerchant === []) {
            return $paymentTransmissionsRequestTransfer;
        }

        $merchantCollectionTransfer = $this->getMerchantCollection($merchantReferences, $paymentTransmissionsRequestTransfer->getTenantIdentifierOrFail());

        foreach ($paymentTransmissionItemsGroupedByOrderReferenceAndMerchant as $paymentTransmissionItemsGrouped) {
            foreach ($paymentTransmissionItemsGrouped as $merchantReference => $merchantData) {
                $merchantTransfer = $this->findMerchantForPaymentTransmissionItem($merchantCollectionTransfer, $merchantReference);

                if (!$merchantTransfer instanceof MerchantTransfer) {
                    $paymentTransmissionTransfer = $this->createFailedPaymentTransmissionTransfer($merchantData, $merchantReference);
                    $paymentTransmissionsRequestTransfer->addFailedPaymentTransmission($paymentTransmissionTransfer);

                    continue;
                }

                $paymentTransmissionTransfer = $this->createSuccessfulPaymentTransmissionTransfer($merchantData, $merchantCollectionTransfer, $merchantReference);
                $paymentTransmissionsRequestTransfer->addPaymentTransmission($paymentTransmissionTransfer);
            }
        }

        return $paymentTransmissionsRequestTransfer;
    }

    protected function findMerchantForPaymentTransmissionItem(
        MerchantCollectionTransfer $merchantCollectionTransfer,
        string $merchantReference
    ): ?MerchantTransfer {
        foreach ($merchantCollectionTransfer->getMerchants() as $merchantTransfer) {
            if ($merchantReference === $merchantTransfer->getMerchantReferenceOrFail()) {
                return $merchantTransfer;
            }
        }

        return null;
    }

    /**
     * @param list<string> $merchantReferences
     * @param string $tenantIdentifier
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    protected function getMerchantCollection(array $merchantReferences, string $tenantIdentifier): MerchantCollectionTransfer
    {
        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())
            ->setMerchantReferences($merchantReferences)
            ->setTenantIdentifier($tenantIdentifier);

        return $this->appMerchantRepository->getMerchantCollection($merchantCriteriaTransfer);
    }

    /**
     * @return array<mixed>
     */
    protected function groupPaymentTransmissionItemsByOrderReferenceAndMerchantReference(
        PaymentTransmissionsRequestTransfer $paymentTransmissionsRequestTransfer
    ): array {
        $merchantReferences = [];
        $paymentTransmissionItemsGroupedByOrderReferenceAndMerchant = [];

        foreach ($paymentTransmissionsRequestTransfer->getPaymentTransmissions() as $paymentTransmissionTransfer) {
            foreach ($paymentTransmissionTransfer->getPaymentTransmissionItems() as $paymentTransmissionItemTransfer) {
                if (!$paymentTransmissionItemTransfer->getMerchantReference()) {
                    continue;
                }

                $orderReference = $paymentTransmissionItemTransfer->getOrderReference();
                $merchantReference = $paymentTransmissionItemTransfer->getMerchantReference();

                if (!isset($paymentTransmissionItemsGroupedByOrderReferenceAndMerchant[$orderReference])) {
                    $paymentTransmissionItemsGroupedByOrderReferenceAndMerchant[$orderReference] = [];
                }

                if (!isset($paymentTransmissionItemsGroupedByOrderReferenceAndMerchant[$orderReference][$merchantReference])) {
                    $paymentTransmissionItemsGroupedByOrderReferenceAndMerchant[$orderReference][$merchantReference] = [
                        static::KEY_PAYMENT_TRANSMISSION_ITEMS => [],
                        static::KEY_PAYMENT_TRANSMISSION => clone $paymentTransmissionTransfer,
                    ];
                }

                $paymentTransmissionItemsGroupedByOrderReferenceAndMerchant[$orderReference][$merchantReference][static::KEY_PAYMENT_TRANSMISSION_ITEMS][] = $paymentTransmissionItemTransfer;
                $merchantReferences[$merchantReference] = $merchantReference;
            }
        }

        return [$paymentTransmissionItemsGroupedByOrderReferenceAndMerchant, $merchantReferences];
    }

    /**
     * @param array<mixed> $merchantData
     * @param \Generated\Shared\Transfer\MerchantCollectionTransfer$merchantCollectionTransfer
     * @param string $merchantReference
     */
    public function createSuccessfulPaymentTransmissionTransfer(
        array $merchantData,
        MerchantCollectionTransfer $merchantCollectionTransfer,
        string $merchantReference
    ): PaymentTransmissionTransfer {
        $paymentTransmissionTransfer = $merchantData[static::KEY_PAYMENT_TRANSMISSION];
        $paymentTransmissionTransfer
            ->setPaymentTransmissionItems(new ArrayObject($merchantData[static::KEY_PAYMENT_TRANSMISSION_ITEMS]))
            ->setMerchant($this->findMerchantForPaymentTransmissionItem($merchantCollectionTransfer, $merchantReference))
            ->setMerchantReference($merchantReference);

        return $paymentTransmissionTransfer;
    }

    /**
     * @param array<mixed> $merchantData
     */
    public function createFailedPaymentTransmissionTransfer(
        array $merchantData,
        string $merchantReference
    ): PaymentTransmissionTransfer {
        $paymentTransmissionTransfer = $merchantData[static::KEY_PAYMENT_TRANSMISSION];
        $paymentTransmissionTransfer
            ->setPaymentTransmissionItems(new ArrayObject($merchantData[static::KEY_PAYMENT_TRANSMISSION_ITEMS]))
            ->setMessage(MessageBuilder::merchantByReferenceNotFound($merchantReference))
            ->setIsSuccessful(false);

        return $paymentTransmissionTransfer;
    }
}
