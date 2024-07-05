<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\MerchantAppOnboarding;

enum MerchantAppOnboardingStrategy
{
    /**
     * Using the API strategy on the App side means that the AppMerchant modules API endpoint will be used to onboard the merchant.
     *
     * @var string
     */
    public const API = 'api';

    /**
     * Using the iFrame strategy on the App side means that the App itself needs to provide the URL for it.
     *
     * @var string
     */
    public const IFRAME = 'iframe';

    /**
     * Using the redirect strategy on the App side means that the App itself needs to provide the URL for it.
     *
     * @var string
     */
    public const REDIRECT = 'redirect';
}
