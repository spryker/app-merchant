<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Glue\AppMerchantBackendApi\Helper;

use Codeception\Module;
use Codeception\Stub;
use Exception;
use Generated\Shared\Transfer\AppConfigTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Glue\AppWebhookBackendApi\AppWebhookBackendApiDependencyProvider;
use Spryker\Glue\AppWebhookBackendApi\Plugin\AppWebhookBackendApi\GlueRequestWebhookMapperPluginInterface;
use Spryker\Shared\Config\Config;
use Spryker\Shared\GlueBackendApiApplication\GlueBackendApiApplicationConstants;
use Spryker\Shared\ZedRequest\ZedRequestConstants;
use Spryker\Zed\AppMerchant\AppMerchantDependencyProvider;
use Spryker\Zed\AppMerchantExtension\Dependency\Plugin\MerchantAppOnboarding\AppMerchantPlatformPluginInterface;
use SprykerTest\Shared\Testify\Helper\DependencyHelperTrait;

class WebhookApiHelper extends Module
{
    use DependencyHelperTrait;

    public function buildWebhookUrl(): string
    {
        return $this->buildBackendApiUrl('webhooks');
    }

    /**
     * @param array<mixed>|string $params
     */
    protected function buildBackendApiUrl(string $url, array $params = []): string
    {
        $url = sprintf('%s://%s/%s', Config::get(ZedRequestConstants::ZED_API_SSL_ENABLED) ? 'https' : 'http', Config::get(GlueBackendApiApplicationConstants::GLUE_BACKEND_API_HOST), $this->formatUrl($url, $params));

        return rtrim($url, '/');
    }

    /**
     * @param array<mixed>|string $params
     */
    protected function formatUrl(string $url, array $params): string
    {
        $refinedParams = [];
        foreach ($params as $key => $value) {
            $refinedParams['{' . $key . '}'] = urlencode($value);
        }

        return strtr($url, $refinedParams);
    }

    public function mockGlueRequestWebhookMapperPlugin(
        string $webhookDataType,
        ?string $merchantReference = null,
        ?string $tenantIdentifier = null
    ): void {
        $glueRequestWebhookMapperPluginMock = Stub::makeEmpty(GlueRequestWebhookMapperPluginInterface::class, [
            'mapGlueRequestDataToWebhookRequestTransfer' => function (GlueRequestTransfer $glueRequestTransfer, WebhookRequestTransfer $webhookRequestTransfer) use ($webhookDataType, $merchantReference, $tenantIdentifier): WebhookRequestTransfer {
                $webhookRequestTransfer
                    ->setType($webhookDataType)
                    ->setMerchantReference($merchantReference)
                    ->setTenantIdentifier($tenantIdentifier);

                return $webhookRequestTransfer;
            },
        ]);

        $this->getDependencyHelper()->setDependency(
            AppWebhookBackendApiDependencyProvider::PLUGIN_GLUE_REQUEST_WEBHOOK_MAPPER,
            $glueRequestWebhookMapperPluginMock,
        );
    }

    public function mockPaymentPlatform(
        string $transactionId,
        bool $webhookResponseSuccessful,
        ?string $accountStatus = null
    ): void {
        $platformPluginMock = Stub::makeEmpty(AppMerchantPlatformPluginInterface::class, [
            'handleWebhook' => function (WebhookRequestTransfer $webhookRequestTransfer) use ($transactionId, $webhookResponseSuccessful, $accountStatus): WebhookResponseTransfer {
                $webhookResponseTransfer = new WebhookResponseTransfer();
                $webhookResponseTransfer->setIsSuccessful($webhookResponseSuccessful);

                if ($accountStatus) {
                    $webhookResponseTransfer->setPaymentStatus($accountStatus);
                }

                // Ensure that the AppConfig is always passed to the platform plugin.
                $this->assertInstanceOf(AppConfigTransfer::class, $webhookRequestTransfer->getAppConfig());

                return $webhookResponseTransfer;
            },
        ]);

        $this->getDependencyHelper()->setDependency(AppMerchantDependencyProvider::PLUGIN_PLATFORM, $platformPluginMock);
    }

    public function mockPaymentPlatformThatThrowsAnException(): void
    {
        $platformPluginMock = Stub::makeEmpty(AppMerchantPlatformPluginInterface::class, [
            'handleWebhook' => function (): WebhookResponseTransfer {
                throw new Exception('AppMerchantPlatformPluginInterface::handleWebhook() exception.');
            },
        ]);

        $this->getDependencyHelper()->setDependency(AppMerchantDependencyProvider::PLUGIN_PLATFORM, $platformPluginMock);
    }
}
