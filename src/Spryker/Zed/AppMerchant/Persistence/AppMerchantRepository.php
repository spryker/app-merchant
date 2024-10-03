<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Persistence;

use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\AppMerchant\Persistence\SpyMerchantQuery;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\AppMerchant\Persistence\AppMerchantPersistenceFactory getFactory()
 */
class AppMerchantRepository extends AbstractRepository implements AppMerchantRepositoryInterface
{
    public function findMerchant(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer
    {
        $spyMerchantQuery = $this->applyMerchantCriteria(
            $this->getFactory()->createMerchantQuery(),
            $merchantCriteriaTransfer,
        );

        $merchantEntity = $spyMerchantQuery->findOne();

        if ($merchantEntity === null) {
            return null;
        }

        return $this->getFactory()->createMerchantMapper()
            ->mapMerchantEntityToMerchantTransfer($merchantEntity, new MerchantTransfer());
    }

    public function getMerchantCollection(
        MerchantCriteriaTransfer $merchantCriteriaTransfer
    ): MerchantCollectionTransfer {
        $spyMerchantQuery = $this->applyMerchantCriteria(
            $this->getFactory()->createMerchantQuery(),
            $merchantCriteriaTransfer,
        );

        $merchantEntityCollection = $spyMerchantQuery->find();

        return $this->getFactory()->createMerchantMapper()
            ->mapMerchantEntityCollectionToMerchantCollectionTransfer($merchantEntityCollection, new MerchantCollectionTransfer());
    }

    protected function applyMerchantCriteria(
        SpyMerchantQuery $spyMerchantQuery,
        MerchantCriteriaTransfer $merchantCriteriaTransfer
    ): SpyMerchantQuery {
        if ($merchantCriteriaTransfer->getMerchantReference() !== null && $merchantCriteriaTransfer->getMerchantReference() !== '' && $merchantCriteriaTransfer->getMerchantReference() !== '0') {
            $spyMerchantQuery->filterByMerchantReference($merchantCriteriaTransfer->getMerchantReference());
        }

        if ($merchantCriteriaTransfer->getMerchantReferences() !== null && $merchantCriteriaTransfer->getMerchantReferences() !== []) {
            $spyMerchantQuery->filterByMerchantReference_In($merchantCriteriaTransfer->getMerchantReferences());
        }

        if ($merchantCriteriaTransfer->getTenantIdentifier() !== null && $merchantCriteriaTransfer->getTenantIdentifier() !== '' && $merchantCriteriaTransfer->getTenantIdentifier() !== '0') {
            $spyMerchantQuery->filterByTenantIdentifier($merchantCriteriaTransfer->getTenantIdentifier());
        }

        return $spyMerchantQuery;
    }
}
