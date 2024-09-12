<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Persistence;

use Generated\Shared\Transfer\MerchantTransfer;

interface AppMerchantEntityManagerInterface
{
    public function saveMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer;

    public function updateMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer;

    public function deleteMerchant(MerchantTransfer $merchantTransfer): void;
}
