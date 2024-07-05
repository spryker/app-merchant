<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding;

enum MerchantAppOnboardingStatus
{
    /**
     * @var string
     */
    public const STATUS_COMPLETED = 'completed';

    /**
     * @var string
     */
    public const STATUS_ENABLED = 'enabled';

    /**
     * @var string
     */
    public const STATUS_RESTRICTED = 'restricted';

    /**
     * @var string
     */
    public const STATUS_RESTRICTED_SOON = 'restricted soon';

    /**
     * @var string
     */
    public const STATUS_REJECTED = 'rejected';

    /**
     * @var string
     */
    public const STATUS_PENDING = 'pending';

    /**
     * @var string
     */
    public const STATUS_INCOMPLETE = 'incomplete';
}
