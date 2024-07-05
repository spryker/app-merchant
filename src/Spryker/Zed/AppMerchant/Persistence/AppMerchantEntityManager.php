<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Persistence;

use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\AppMerchant\Persistence\AppMerchantPersistenceFactory getFactory()
 */
class AppMerchantEntityManager extends AbstractEntityManager implements AppMerchantEntityManagerInterface
{
    public function saveMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        $spyMerchantEntity = $this->getFactory()->createMerchantQuery()
            ->filterByMerchantReference($merchantTransfer->getMerchantReferenceOrFail())
            ->filterByTenantIdentifier($merchantTransfer->getTenantIdentifierOrFail())
            ->findOneOrCreate();

        // Keep this here as the mapper would set it always to false after save
        // This is required to return either 200 (OK) or 201 (CREATED) status code
        // The merchant could be saved two times in the process.
        // 1. In the beginning of the onboarding process
        // 2. After the platform plugin execution which could have manipulated the Merchant
        $isNew = $merchantTransfer->getIsNew() ?? $spyMerchantEntity->isNew();

        if ($isNew) {
            $merchantAppConfig = $merchantTransfer->modifiedToArray();
            $merchantAppConfig[MerchantTransfer::CONFIG] = json_encode($merchantTransfer->getConfig());

            $spyMerchantEntity->fromArray($merchantAppConfig);
            $spyMerchantEntity->save();
        }

        $merchantTransfer = $this->getFactory()->createMerchantMapper()->mapMerchantEntityToMerchantTransfer($spyMerchantEntity, $merchantTransfer);
        $merchantTransfer->setIsNew($isNew);

        return $merchantTransfer;
    }

    public function updateMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        /** @phpstan-var \Orm\Zed\AppMerchant\Persistence\SpyMerchant */
        $spyMerchantEntity = $this->getFactory()->createMerchantQuery()
            ->filterByMerchantReference($merchantTransfer->getMerchantReferenceOrFail())
            ->filterByTenantIdentifier($merchantTransfer->getTenantIdentifierOrFail())
            ->findOne();

        $merchantAppConfig = $merchantTransfer->modifiedToArray();
        $merchantAppConfig[MerchantTransfer::CONFIG] = json_encode($merchantTransfer->getConfig());

        $spyMerchantEntity->fromArray($merchantAppConfig);
        $spyMerchantEntity->save();

        return $this->getFactory()->createMerchantMapper()->mapMerchantEntityToMerchantTransfer($spyMerchantEntity, $merchantTransfer);
    }
}
