<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\AppMerchant\Business\Message;

class AppMerchantMessage
{
    public static function appNameNotDefinedExceptionMessage(): string
    {
        return 'App name is not defined. The app name is required to for the Onboarding process and will be shown on the Tenant Side to distinguish between different Apps (for humans).';
    }

    public static function appIdentifierNotDefinedExceptionMessage(): string
    {
        return 'App identifier is not defined. The app identifier is required to for the Onboarding process and will be used on the Tenant Side to distinguish between different Apps (for machines).';
    }

    public static function typeNotDefinedExceptionMessage(): string
    {
        return 'The type is not defined. The type is required to for the Onboarding process and will be used on the Tenant Side to distinguish between different onboarding types for the same App.';
    }

    public static function onboardingStrategyNotDefinedExceptionMessage(): string
    {
        return 'The onboarding strategy is not defined. The onboarding strategy is required to for the Onboarding process and will be used on the Tenant Side to run the correct strategy.';
    }

    public static function onboardingUrlNotDefinedExceptionMessage(): string
    {
        return 'The onboarding URL is not defined. The onboarding URL is required to for the Onboarding process and will be used on the Tenant Side.';
    }
}
