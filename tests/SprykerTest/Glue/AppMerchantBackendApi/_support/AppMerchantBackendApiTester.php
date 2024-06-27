<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Glue\AppMerchantBackendApi;

use Orm\Zed\AppMerchant\Persistence\SpyMerchant;
use Orm\Zed\AppMerchant\Persistence\SpyMerchantQuery;
use SprykerTest\Glue\Testify\Tester\ApiEndToEndTester;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(\SprykerTest\Glue\AppMerchantBackendApi\PHPMD)
 */
class AppMerchantBackendApiTester extends ApiEndToEndTester
{
    use _generated\AppMerchantBackendApiTesterActions;

    /**
     * @return void
     */
    public function seeResponseJsonContainsMerchantAppOnboarding(string $tenantIdentifier, string $merchantReference): void
    {
        $this->seeResponseJsonPathContains(['tenant_identifier' => $tenantIdentifier, 'merchant_reference' => $merchantReference]);
    }

    /**
     * @return void
     */
    public function seeResponseJsonContainsStrategyAndUrlFromPlatformPluginImplementation(string $strategy, string $url): void
    {
        $this->seeResponseJsonPathContains(['strategy' => $strategy, 'url' => $url]);
    }

    /**
     * @param string $message
     * @param int|null $code
     * @param int|null $status
     *
     * @return void
     */
    public function seeResponseJsonContainsError(string $message, ?int $code = null, ?int $status = null): void
    {
        $this->seeResponseJsonPathContains(['errors' => [['code' => $code, 'status' => $status, 'message' => $message]]]);
    }

    public function seeMerchantInDatabase(string $merchantReference, array $expectedMerchant): void
    {
        $spyMerchantEntity = SpyMerchantQuery::create()
            ->filterByMerchantReference($merchantReference)
            ->findOne();

        $this->assertInstanceOf(SpyMerchant::class, $spyMerchantEntity);
        $this->assertSame(json_decode($spyMerchantEntity->getConfig(), true), $expectedMerchant);
    }
}
