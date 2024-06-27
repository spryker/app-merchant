# AppMerchant Module
[![Latest Stable Version](https://poser.pugx.org/spryker/app-merchant/v/stable.svg)](https://packagist.org/packages/spryker/app-merchant)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)](https://php.net/)

Provides SyncAPI and AsyncAPI schema files and the needed code to be used in a Payment Service Provider App.

## Installation

```
composer require spryker/app-merchant
```

### Configure

#### App Identifier

config/Shared/config_default.php

```
use Spryker\Shared\AppMerchant\AppConstants;

$config[AppConstants::APP_IDENTIFIER] = getenv('APP_IDENTIFIER') ?: 'hello-world';
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

## Plugins

The following plugins can be used inside your Payment Service Provider App.

### GlueApplication

#### \Spryker\Glue\AppMerchantBackendApi\Plugin\GlueApplication\AppMerchantBackendApiRouteProviderPlugin

This plugin provides the routes for the AppMerchantBackendApi module.


###### Routes provided

- /private/initialize-payment - Used from the Tenant side to initialize a payment.


### AppKernel
- \Spryker\Glue\AppMerchantBackendApi\Plugin\AppKernel\PaymentConfigurationValidatorPlugin
- \Spryker\Zed\AppMerchant\Communication\Plugin\AppKernel\DeleteTenantPaymentsConfigurationAfterDeletePlugin
- \Spryker\Zed\AppMerchant\Communication\Plugin\AppKernel\SendAddPaymentMethodMessageConfigurationAfterSavePlugin
- \Spryker\Zed\AppMerchant\Communication\Plugin\AppKernel\SendDeletePaymentMethodMessageConfigurationAfterDeletePlugin

### AppWebhook
- \Spryker\Zed\AppMerchant\Communication\Plugin\AppWebhook\PaymentWebhookHandlerPlugin

### MessageBroker
- \Spryker\Zed\AppMerchant\Communication\Plugin\MessageBroker\CancelPaymentMessageHandlerPlugin
- \Spryker\Zed\AppMerchant\Communication\Plugin\MessageBroker\CapturePaymentMessageHandlerPlugin
- \Spryker\Zed\AppMerchant\Communication\Plugin\MessageBroker\RefundPaymentMessageHandlerPlugin

### MessageBrokerAws
- \Spryker\Zed\AppMerchant\Communication\Plugin\MessageBrokerAws\ConsumerIdHttpChannelMessageReceiverRequestExpanderPlugin
