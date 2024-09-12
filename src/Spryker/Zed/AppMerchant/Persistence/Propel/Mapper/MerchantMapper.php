<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\AppMerchant\Persistence\SpyMerchant;
use Propel\Runtime\Collection\Collection;

class MerchantMapper
{
    public function mapMerchantEntityCollectionToMerchantCollectionTransfer(
        Collection $merchantEntityCollection,
        MerchantCollectionTransfer $merchantCollectionTransfer
    ): MerchantCollectionTransfer {
        foreach ($merchantEntityCollection as $merchantEntity) {
            $merchantCollectionTransfer->addMerchant(
                $this->mapMerchantEntityToMerchantTransfer($merchantEntity, new MerchantTransfer()),
            );
        }

        return $merchantCollectionTransfer;
    }

    public function mapMerchantEntityToMerchantTransfer(
        SpyMerchant $spyMerchant,
        MerchantTransfer $merchantTransfer
    ): MerchantTransfer {
        $merchantTransfer = $merchantTransfer->fromArray($spyMerchant->toArray(), true);

        /** @var array<string, mixed> $config */
        $config = json_decode((string)$spyMerchant->getConfig(), true);
        $merchantTransfer->setConfig($config);

        return $merchantTransfer;
    }
}
