<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Persistence;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\AppMerchant\Persistence\AppMerchantPersistenceFactory getFactory()
 */
class AppMerchantRepository extends AbstractRepository implements AppMerchantRepositoryInterface
{
    public function findMerchant(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer
    {
        $spyMerchantQuery = $this->getFactory()->createMerchantQuery();
        $merchantEntity = $spyMerchantQuery
            ->filterByMerchantReference($merchantCriteriaTransfer->getMerchantReferenceOrFail())
            ->filterByTenantIdentifier($merchantCriteriaTransfer->getTenantIdentifierOrFail());

        $merchantEntity = $merchantEntity->findOne();

        if ($merchantEntity === null) {
            return null;
        }

        return $this->getFactory()->createMerchantMapper()
            ->mapMerchantEntityToMerchantTransfer($merchantEntity, new MerchantTransfer());
    }

    /**
     * @return array<\Generated\Shared\Transfer\MerchantTransfer>
     */
    public function findMerchants(MerchantCriteriaTransfer $merchantCriteriaTransfer): array
    {
        $spyMerchantQuery = $this->getFactory()->createMerchantQuery();
        $merchantEntityQuery = $spyMerchantQuery
            ->filterByMerchantReference_In($merchantCriteriaTransfer->getMerchantReferences())
            ->filterByTenantIdentifier($merchantCriteriaTransfer->getTenantIdentifierOrFail());

        $merchantEntityCollection = $merchantEntityQuery->find();

        $merchantTransfers = [];

        /** @var \Orm\Zed\AppMerchant\Persistence\SpyMerchant $merchantEntity */
        foreach ($merchantEntityCollection as $merchantEntity) {
            $merchantTransfers[] = $this->getFactory()->createMerchantMapper()
                ->mapMerchantEntityToMerchantTransfer($merchantEntity, new MerchantTransfer());
        }

        return $merchantTransfers;
    }
}
