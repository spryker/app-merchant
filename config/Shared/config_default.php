<?php

/**
 * This configuration is used for TESTING only and will never be used in production!
 */

use Generated\Shared\Transfer\MerchantAppOnboardingStatusChangedTransfer;
use Generated\Shared\Transfer\ReadyForMerchantAppOnboardingTransfer;
use Ramsey\Uuid\Uuid;
use Spryker\Shared\AppKernel\AppKernelConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Application\Log\Config\SprykerLoggerConfig;
use Spryker\Shared\ErrorHandler\ErrorHandlerConstants;
use Spryker\Shared\GlueBackendApiApplication\GlueBackendApiApplicationConstants;
use Spryker\Shared\GlueJsonApiConvention\GlueJsonApiConventionConstants;
use Spryker\Shared\Http\HttpConstants;
use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Shared\Log\LogConstants;
use Spryker\Shared\MessageBroker\MessageBrokerConstants;
use Spryker\Shared\MessageBrokerAws\MessageBrokerAwsConstants;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Shared\ZedRequest\ZedRequestConstants;
use Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig;
use Spryker\Zed\PropelOrm\Business\Builder\ExtensionObjectBuilder;
use Spryker\Zed\PropelOrm\Business\Builder\ExtensionQueryBuilder;
use Spryker\Zed\PropelOrm\Business\Builder\ObjectBuilder;
use Spryker\Zed\PropelOrm\Business\Builder\QueryBuilder;

// ----------------------------------------------------------------------------
// ------------------------------ Glue Backend API ----------------------------
// ----------------------------------------------------------------------------
$config[GlueBackendApiApplicationConstants::GLUE_BACKEND_API_HOST] = 'api.merchant.local';

$config[KernelConstants::ENABLE_CONTAINER_OVERRIDING] = true;
$config[KernelConstants::PROJECT_NAMESPACES] =
$config[GlueBackendApiApplicationConstants::PROJECT_NAMESPACES] = [
    'Spryker',
];
$config[ZedRequestConstants::ZED_API_SSL_ENABLED] = (bool)getenv('SPRYKER_ZED_SSL_ENABLED');

$config[ApplicationConstants::BASE_URL_ZED] = sprintf(
    'https://%s',
    'api.merchanr.local',
);

$config[AppKernelConstants::APP_IDENTIFIER] = Uuid::uuid4()->toString();

$config[HttpConstants::URI_SIGNER_SECRET_KEY] = Uuid::uuid4()->toString();

$config[GlueJsonApiConventionConstants::GLUE_DOMAIN] = sprintf(
    '%s://%s',
    getenv('SPRYKER_SSL_ENABLE') ? 'https' : 'http',
    $config[GlueBackendApiApplicationConstants::GLUE_BACKEND_API_HOST] ?: 'localhost',
);

$config[MessageBrokerConstants::CHANNEL_TO_SENDER_TRANSPORT_MAP] = [
    'app-events' => MessageBrokerAwsConfig::HTTP_CHANNEL_TRANSPORT,
];

$connections = [
    'mysql' => [
        'adapter' => 'sqlite',
        'dsn' => 'sqlite:tests/_data/app_merchant_db',
        'user' => '',
        'password' => '',
        'settings' => [],
    ],
];

$config[PropelConstants::PROPEL] = [
    'database' => [
        'connections' => [],
    ],
    'runtime' => [
        'defaultConnection' => 'default',
        'connections' => ['default', 'zed'],
    ],
    'generator' => [
        'defaultConnection' => 'default',
        'connections' => ['default', 'zed'],
        'objectModel' => [
            'defaultKeyType' => 'fieldName',
            'builders' => [
                // If you need full entity logging on Create/Update/Delete, then switch to
                // Spryker\Zed\PropelOrm\Business\Builder\ObjectBuilderWithLogger instead.
                'object' => ObjectBuilder::class,
                'objectstub' => ExtensionObjectBuilder::class,
                'query' => QueryBuilder::class,
                'querystub' => ExtensionQueryBuilder::class,
            ],
        ],
    ],
    'paths' => [
        'phpDir' => APPLICATION_ROOT_DIR,
        'sqlDir' => APPLICATION_SOURCE_DIR . '/Orm/Propel/Sql/',
        'migrationDir' => APPLICATION_SOURCE_DIR . '/Orm/Propel/Migration_SQLite/',
        'schemaDir' => APPLICATION_SOURCE_DIR . '/Orm/Propel/Schema/',
    ],
];

$config[PropelConstants::ZED_DB_ENGINE] = 'sqlite';
$config[PropelConstants::ZED_DB_HOST] = 'localhost';
$config[PropelConstants::ZED_DB_PORT] = 1234;
$config[PropelConstants::ZED_DB_USERNAME] = 'catface';
$config[PropelConstants::ZED_DB_PASSWORD] = 'catface';

$config[PropelConstants::PROPEL]['database']['connections']['default'] = $connections['mysql'];
$config[PropelConstants::PROPEL]['database']['connections']['zed'] = $connections['mysql'];

$config[KernelConstants::PROJECT_NAMESPACE] = 'Spryker';
$config[KernelConstants::PROJECT_NAMESPACES] = ['Spryker'];
$config[KernelConstants::CORE_NAMESPACES] = ['Spryker'];
$config[KernelConstants::ENABLE_CONTAINER_OVERRIDING] = true;
$config[ErrorHandlerConstants::ERROR_LEVEL] = E_ALL & ~E_DEPRECATED;
$config[LogConstants::LOGGER_CONFIG] = SprykerLoggerConfig::class;
$config[LogConstants::LOG_FILE_PATH] = sys_get_temp_dir() . '/logs';

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
