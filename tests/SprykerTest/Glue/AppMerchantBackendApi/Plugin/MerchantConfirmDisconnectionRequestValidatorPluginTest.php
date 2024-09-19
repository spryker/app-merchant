<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Glue\AppMerchantBackendApi\Plugin;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueErrorConfirmTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Glue\AppKernel\AppKernelConfig;
use Spryker\Glue\AppMerchantBackendApi\AppMerchantBackendApiConfig;
use Spryker\Glue\AppMerchantBackendApi\AppMerchantBackendApiDependencyProvider;
use Spryker\Glue\AppMerchantBackendApi\Dependency\Facade\AppMerchantBackendApiToTranslatorFacadeInterface;
use Spryker\Glue\AppMerchantBackendApi\Plugin\GlueApplication\MerchantConfirmDisconnectionRequestValidatorPlugin;
use Spryker\Glue\AppPaymentBackendApi\Mapper\Payment\GlueRequestPaymentMapper;
use SprykerTest\Glue\AppMerchantBackendApi\AppMerchantBackendApiPluginTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group AppMerchantBackendApi
 * @group Plugin
 * @group MerchantConfirmDisconnectionRequestValidatorPluginTest
 * Add your own group annotations below this line
 */
class MerchantConfirmDisconnectionRequestValidatorPluginTest extends Unit
{
    /**
     * @var string
     */
    protected const TENANT_IDENTIFIER = 'tenant-identifier';

    /**
     * @see \Spryker\Glue\AppKernel\Plugin\GlueApplication\AbstractConfirmDisconnectionRequestValidatorPlugin::HEADER_CONFIRMATION_STATUS
     *
     * @var string
     */
    protected const HEADER_CONFIRMATION_STATUS = 'x-confirmation-status';

    protected AppMerchantBackendApiPluginTester $tester;

    public function testMerchantConfirmDisconnectionRequestValidatorPluginReturnsErrorIfThereIsNoExistingTenant(): void
    {
        // Arrange
        $this->tester->setDependency(AppMerchantBackendApiDependencyProvider::FACADE_TRANSLATOR, $this->getTranslatorFacadeMock());

        $merchantConfirmDisconnectionRequestValidatorPlugin = new MerchantConfirmDisconnectionRequestValidatorPlugin();

        // Act
        $glueRequestValidationTransfer = $merchantConfirmDisconnectionRequestValidatorPlugin
            ->validate(new GlueRequestTransfer());

        // Assert
        $this->assertFalse($glueRequestValidationTransfer->getIsValid());
        $this->assertCount(1, $glueRequestValidationTransfer->getErrors());
        $this->assertSame(
            AppKernelConfig::ERROR_CODE_PAYMENT_DISCONNECTION_TENANT_IDENTIFIER_MISSING,
            $glueRequestValidationTransfer->getErrors()[0]->getCode(),
        );
    }

    public function testMerchantConfirmDisconnectionRequestValidatorPluginReturnsSuccessIfThereAreNoMerchantsForExistingTenant(): void
    {
        // Arrange
        $this->tester->setDependency(AppMerchantBackendApiDependencyProvider::FACADE_TRANSLATOR, $this->getTranslatorFacadeMock());

        $merchantConfirmDisconnectionRequestValidatorPlugin = new MerchantConfirmDisconnectionRequestValidatorPlugin();
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer->setMeta([
            GlueRequestPaymentMapper::HEADER_TENANT_IDENTIFIER => [static::TENANT_IDENTIFIER],
        ]);

        // Act
        $glueRequestValidationTransfer = $merchantConfirmDisconnectionRequestValidatorPlugin
            ->validate($glueRequestTransfer);

        // Assert
        $this->assertTrue($glueRequestValidationTransfer->getIsValid());
    }

    public function testMerchantConfirmDisconnectionRequestValidatorPluginReturnsErrorIfThereAreMerchantsForExistingTenant(): void
    {
        // Arrange
        $this->tester->setDependency(AppMerchantBackendApiDependencyProvider::FACADE_TRANSLATOR, $this->getTranslatorFacadeMock());

        $merchantConfirmDisconnectionRequestValidatorPlugin = new MerchantConfirmDisconnectionRequestValidatorPlugin();
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer->setMeta([
            GlueRequestPaymentMapper::HEADER_TENANT_IDENTIFIER => [static::TENANT_IDENTIFIER],
        ]);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::TENANT_IDENTIFIER => static::TENANT_IDENTIFIER,
        ]);

        // Act
        $glueRequestValidationTransfer = $merchantConfirmDisconnectionRequestValidatorPlugin
            ->validate($glueRequestTransfer);

        // Assert
        $this->assertFalse($glueRequestValidationTransfer->getIsValid());
        $this->assertCount(1, $glueRequestValidationTransfer->getErrors());
        $this->assertSame(
            AppMerchantBackendApiConfig::ERROR_CODE_PAYMENT_DISCONNECTION_CANNOT_BE_PROCEEDED,
            $glueRequestValidationTransfer->getErrors()[0]->getCode(),
        );
        $this->assertInstanceOf(GlueErrorConfirmTransfer::class, $glueRequestValidationTransfer->getErrors()[0]->getConfirm());
    }

    /**
     * @dataProvider confirmationStatusDataProvider
     *
     * @param string $confirmationStatus
     *
     * @return void
     */
    public function testMerchantConfirmDisconnectionRequestValidatorPluginReturnsErrorIfThereAreMerchantsForExistingTenantAndTheRequestContainsConfirmationCanceledResponse(
        string $confirmationStatus
    ): void {
        // Arrange
        $this->tester->setDependency(AppMerchantBackendApiDependencyProvider::FACADE_TRANSLATOR, $this->getTranslatorFacadeMock());

        $merchantConfirmDisconnectionRequestValidatorPlugin = new MerchantConfirmDisconnectionRequestValidatorPlugin();
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer->setMeta([
            GlueRequestPaymentMapper::HEADER_TENANT_IDENTIFIER => [static::TENANT_IDENTIFIER],
            static::HEADER_CONFIRMATION_STATUS => [$confirmationStatus],
        ]);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::TENANT_IDENTIFIER => static::TENANT_IDENTIFIER,
        ]);

        // Act
        $glueRequestValidationTransfer = $merchantConfirmDisconnectionRequestValidatorPlugin
            ->validate($glueRequestTransfer);

        // Assert
        $this->assertFalse($glueRequestValidationTransfer->getIsValid());
        $this->assertCount(1, $glueRequestValidationTransfer->getErrors());
        $this->assertSame(
            AppMerchantBackendApiConfig::ERROR_CODE_PAYMENT_DISCONNECTION_FORBIDDEN,
            $glueRequestValidationTransfer->getErrors()[0]->getCode(),
        );
    }

    public function testMerchantConfirmDisconnectionRequestValidatorPluginReturnsSuccessIfThereAreMerchantsForExistingTenantAndTheRequestContainsConfirmationSuccessfulResponse(): void
    {
        // Arrange
        $this->tester->setDependency(AppMerchantBackendApiDependencyProvider::FACADE_TRANSLATOR, $this->getTranslatorFacadeMock());

        $merchantConfirmDisconnectionRequestValidatorPlugin = new MerchantConfirmDisconnectionRequestValidatorPlugin();
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer->setMeta([
            GlueRequestPaymentMapper::HEADER_TENANT_IDENTIFIER => [static::TENANT_IDENTIFIER],
            static::HEADER_CONFIRMATION_STATUS => ['true'],
        ]);

        $this->tester->haveMerchantPersisted([
            MerchantTransfer::TENANT_IDENTIFIER => static::TENANT_IDENTIFIER,
        ]);

        // Act
        $glueRequestValidationTransfer = $merchantConfirmDisconnectionRequestValidatorPlugin
            ->validate($glueRequestTransfer);

        // Assert
        $this->assertTrue($glueRequestValidationTransfer->getIsValid());
    }

    protected function getTranslatorFacadeMock(): AppMerchantBackendApiToTranslatorFacadeInterface
    {
        return $this->getMockBuilder(AppMerchantBackendApiToTranslatorFacadeInterface::class)->getMock();
    }

    /**
     * @return array<string, array<string>>
     */
    public function confirmationStatusDataProvider(): array
    {
        return [
            'confirmation status false' => ['false'],
            'confirmation status double false' => ['false,true'],
            'confirmation status any value' => ['value'],
        ];
    }
}
