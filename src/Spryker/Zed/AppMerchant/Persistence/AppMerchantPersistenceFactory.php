<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Persistence;

use Orm\Zed\AppMerchant\Persistence\SpyMerchantQuery;
use Spryker\Zed\AppMerchant\Persistence\Propel\Mapper\MerchantMapper;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Spryker\Zed\AppMerchant\Persistence\AppMerchantEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\AppMerchant\Persistence\AppMerchantRepositoryInterface getRepository()
 * @method \Spryker\Zed\AppMerchant\AppMerchantConfig getConfig()
 */
class AppMerchantPersistenceFactory extends AbstractPersistenceFactory
{
    public function createMerchantQuery(): SpyMerchantQuery
    {
        return SpyMerchantQuery::create();
    }

    public function createMerchantMapper(): MerchantMapper
    {
        return new MerchantMapper();
    }
}
