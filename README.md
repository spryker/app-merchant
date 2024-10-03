# AppMerchant Module
[![Latest Stable Version](https://poser.pugx.org/spryker/app-merchant/v/stable.svg)](https://packagist.org/packages/spryker/app-merchant)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-8892BF.svg)](https://php.net/)

Provides SyncAPI and AsyncAPI schema files and the needed code to be used in a Payment Service Provider App.

## Installation

```
composer require spryker/app-merchant
```

### Testing the AppMerchant

You can test the AppMerchant as usual with Codeception. Before that you need to run some commands:

```
composer setup
```

With these commands you've set up the AppMerchant and can start the tests

```
vendor/bin/codecept build
vendor/bin/codecept run
```

# Documentation

The `AppMerchant` package contains the Spryker Glue Application code with a controller and a routing plugin that enables the [Mini-Framework](https://github.com/spryker-projects/mini-framework) to onboard Spryker Merchants to an App. The Route Plugin provides the following URL:

- `/merchants-onboarding`

This endpoint is used by Merchants after the Spryker application (tenant) is notified via the `ReadyForMerchantAppOnboarding` message. When an App uses the `AppMerchant` package and needs Merchant Onboarding to the App the `\Spryker\Zed\AppMerchant\Communication\Plugin\AppKernel\InformTenantAboutMerchantAppOnboardingReadinessConfigurationAfterSavePlugin` needs to be added on the App's project level code.

## High-Level Architecture

[<img alt="High-Level Architecture" width="auto" src="docs/images/app-merchant-high-level-architecture.svg" />](https://docs.spryker.com/)

## Features

### Tenant Onboarding

Before Merchants can be boarded to an App it is required that the Tenant's application is enabled to use this App. When an App config is created/updated the `ReadyForMerchantAppOnboarding` message will be sent to the Tenant. It contains the following information which can be different for each App implementation:

- `onboarding` - This field contains the supported onboarding logic.
- `type` - The type of onboarding, every App can have multiple onboardings e.g. for legal reasons and for the App functionality itself, etc.
- `appName` - The name of the App.
- `appIdentifier` - The identifier of the App.
- `additionalLinks` - A list of Links that can be displayed in the Merchant Portal.
- `merchantOnboardingStates` - A list of possible states the onboarding of a Merchant can have. Each state can have attributes that are used on the Tenant side for displaying purposes.
    - `statusText` - The text that should be displayed as status. (Can be translated on the Tenant side)
    - `displayText` - An additional text that can be displayed alongside the statusText. (Can be translated on the Tenant side)
    - `buttonText` - The text that will be used in the button on the onboarding page. (Can be translated on the Tenant side)
    - `buttonInfo` - An additional text that can be displayed alongside the buttonText. (Can be translated on the Tenant side)

Further reading: https://spryker.atlassian.net/wiki/spaces/AOP/pages/4179623996

### Merchant Onboarding

This is the process where Merchants of a Tenant go through the onboarding process provided by the App.

## Configuration

### Configure the MessageBroker

Add the following to your project:

```
$config[MessageBrokerConstants::MESSAGE_TO_CHANNEL_MAP] =
$config[MessageBrokerAwsConstants::MESSAGE_TO_CHANNEL_MAP] = [
    ReadyForMerchantAppOnboardingTransfer::class => 'merchant-app-events',
    MerchantAppOnboardingStatusChangedTransfer::class => 'merchant-app-events',
];

$config[MessageBrokerConstants::CHANNEL_TO_TRANSPORT_MAP] = [
    'merchant-app-events' => MessageBrokerAwsConfig::HTTP_TRANSPORT,
];

$config[MessageBrokerAwsConstants::CHANNEL_TO_SENDER_TRANSPORT_MAP] = [
    'merchant-app-events' => MessageBrokerAwsConfig::HTTP_TRANSPORT,
];
```

## Database

This package adds the following database tables to the App:

- `spy_merchant`

This package updates the following database table:

- `spy_payment_transfer`
    - The column `merchant_reference` will be added to the table
    - The unique key will be updated

## Plugins

This package provides the following plugins

### Glue

- `\Spryker\Glue\AppMerchantBackendApi\Plugin\GlueApplication\MerchantAppOnboardingBackendApiRouteProviderPlugin`

### Zed

- `\Spryker\Zed\AppMerchant\Communication\Plugin\AppKernel\InformTenantAboutMerchantAppOnboardingReadinessConfigurationAfterSavePlugin`
- `\Spryker\Zed\AppMerchant\Communication\Plugin\AppPayment\MerchantsPaymentTransmissionsRequestExtenderPlugin`
- `\Spryker\Zed\AppMerchant\Communication\Plugin\AppWebhook\AccountUpdatedWebhookHandlerPlugin`

#### MerchantAppOnboardingBackendApiRouteProviderPlugin

This plugin can be added to the `\Pyz\Glue\GlueBackendApiApplication\GlueBackendApiApplicationDependencyProvider::getRouteProviderPlugins()`.

#### InformTenantAboutMerchantAppOnboardingReadinessConfigurationAfterSavePlugin

This plugin can be added to the `\Pyz\Zed\AppKernel\AppKernelDependencyProvider::getConfigurationAfterSavePlugins()` that is explained in the [AppKernel](https://github.com/spryker/app-kernel) documentation. When this plugin is used and the configuration of an App gets created/updated the `ReadyForMerchantAppOnboarding` message will be sent to the Tenant. See Tenant Onboarding.

#### MerchantsPaymentTransmissionsRequestExtenderPlugin

This plugin can be added to the `\Pyz\Zed\AppPayment\AppPaymentDependencyProvider::getPaymentTransmissionRequestExtenderPlugins()` that is explained in the AppPayment documentation. When this plugin is used and via the AppPayment package a “payout“ is requested this plugin groups orders for “payouts“ by Merchants so each Merchant will get all payments for items of one Order transferred to him.

#### AccountUpdatedWebhookHandlerPlugin

This plugin can be added to the `\Pyz\Zed\AppWebhook\AppWebhookDependencyProvider::getWebhookHandlerPlugins()` that is explained in the AppWebhook documentation. When this plugin is used and can handle a webhook that comes from a third-party provider for notifications of an updated account on the third-party side.

Inside of this plugin, the `\Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface::handleWebhook()` method will be used. The `WebhookRequestTransfer` object will contain the loaded AppConfig and the Merchant. Depending on the implementation of the App you will get back a `WebhookResponseTransfer` that will be used to update the Merchant Onboarding status on the Tenant side as well as in the database of the App itself. To do so, a `MerchantAppOnboardingStatusChanged` message will be sent. This message is handled on the Tenant side and updates the database as needed.


## Extension

This package provides the following extension points

### Zed

- `\Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface`

#### AppMerchantPlatformPluginInterface

This extension provides the following methods:

- `provideOnboardingDetails` - Used on the App implementation to provide details of how the App can be used to onboard Merchants.
- `onboardMerchant` - Used on the App implementation to do the Merchant onboarding to the App.
- `handleWebhook` - Used on the App implementation to handle Merchant-related Webhooks.
