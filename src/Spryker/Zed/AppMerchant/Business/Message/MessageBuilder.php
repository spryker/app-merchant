<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\Message;

class MessageBuilder
{
    public static function couldNotFindAMerchantReference(): string
    {
        return 'Could not find a Merchant reference';
    }

    public static function couldNotFindATenantIdentifier(): string
    {
        return 'Could not find a Tenant identifier';
    }

    public static function couldNotFindAMerchant(string $merchantReference, string $tenantIdentifier): string
    {
        return sprintf('Could not find a Merchant by merchantReference "%s" and TenantIdentifier "%s"', $merchantReference, $tenantIdentifier);
    }

    public static function merchantByReferenceNotFound(string $merchantReference): string
    {
        return sprintf('Merchant with reference "%s" not found.', $merchantReference);
    }
}
