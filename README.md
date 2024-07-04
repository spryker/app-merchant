# AppMerchant Package
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
