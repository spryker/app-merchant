<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Shared\AppMerchant\Helper;

use Codeception\Module;

trait AppMerchantHelperTrait
{
    /**
     * @return \SprykerTest\Shared\AppMerchant\Helper\AppMerchantHelper
     */
    protected function getAppMerchantHelper(): AppMerchantHelper
    {
        /** @var \SprykerTest\Shared\AppMerchant\Helper\AppMerchantHelper $appMerchantHelper */
        $appMerchantHelper = $this->getModule('\\' . AppMerchantHelper::class);

        return $appMerchantHelper;
    }

    /**
     * @param string $name
     *
     * @return \Codeception\Module
     */
    abstract protected function getModule(string $name): Module;
}
