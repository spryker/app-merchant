<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Persistence;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

interface AppMerchantRepositoryInterface
{
    public function findMerchant(
        MerchantCriteriaTransfer $merchantCriteriaTransfer
    ): ?MerchantTransfer;

    /**
     * @return array<\Generated\Shared\Transfer\MerchantTransfer>
     */
    public function findMerchants(
        MerchantCriteriaTransfer $merchantCriteriaTransfer
    ): array;
}
