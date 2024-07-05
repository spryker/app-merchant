<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\AppMerchant\Persistence\SpyMerchant;

class MerchantMapper
{
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
