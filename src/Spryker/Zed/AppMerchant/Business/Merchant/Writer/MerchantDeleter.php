<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\Merchant\Writer;

use Generated\Shared\Transfer\AppConfigTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Spryker\Zed\AppMerchant\Business\Message\MessageSender;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantEntityManagerInterface;
use Spryker\Zed\AppMerchant\Persistence\AppMerchantRepositoryInterface;

class MerchantDeleter
{
    public function __construct(
        protected MessageSender $messageSender,
        protected AppMerchantRepositoryInterface $appMerchantRepository,
        protected AppMerchantEntityManagerInterface $appMerchantEntityManager
    ) {
    }

    public function deleteMerchantByAppConfig(AppConfigTransfer $appConfigTransfer): void
    {
        $merchantCollectionTransfer = $this->appMerchantRepository->getMerchantCollection(
            (new MerchantCriteriaTransfer())
                ->setTenantIdentifier($appConfigTransfer->getTenantIdentifier()),
        );

        foreach ($merchantCollectionTransfer->getMerchants() as $merchantTransfer) {
            $this->appMerchantEntityManager->deleteMerchant($merchantTransfer);
        }
    }
}
