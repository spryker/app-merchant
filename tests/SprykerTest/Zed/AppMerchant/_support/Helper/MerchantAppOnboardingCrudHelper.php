<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Zed\AppMerchant\Helper;

use Codeception\Module;
use Generated\Shared\DataBuilder\MerchantAppOnboardingBuilder;
use Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer;
use Generated\Shared\Transfer\MerchantAppOnboardingTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\AppMerchant\Business\AppMerchantFacade;
use Spryker\Zed\AppMerchant\Business\AppMerchantFacadeInterface;
use SprykerTest\Shared\AppKernel\Helper\AppConfigHelperTrait;
use SprykerTest\Shared\AppMerchant\Helper\AppMerchantHelperTrait;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Zed\Testify\Helper\Business\BusinessHelperTrait;

class MerchantAppOnboardingCrudHelper extends Module
{
    use DataCleanupHelperTrait;
    use BusinessHelperTrait;
    use AppMerchantHelperTrait;
    use AppConfigHelperTrait;

    /**
     * @param array<string, mixed> $seed
     *
     * @return \Generated\Shared\Transfer\MerchantAppOnboardingTransfer
     */
    public function haveMerchantAppOnboardingTransfer(array $seed = []): MerchantAppOnboardingTransfer
    {
        $merchantAppOnboardingBuilder = new MerchantAppOnboardingBuilder($seed);

        return $merchantAppOnboardingBuilder->build();
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return void
     */
    public function assertMerchantAppOnboardingResponseContainsMerchantTransfer(
        MerchantAppOnboardingResponseTransfer $merchantAppOnboardingResponseTransfer,
        MerchantTransfer $merchantTransfer
    ): void {
        $this->assertSame(
            $merchantTransfer->getMerchantReferenceOrFail(),
            $merchantAppOnboardingResponseTransfer->getMerchantOrFail()->getMerchantReferenceOrFail(),
            'Expected to have the same merchant reference in the response as in the transfer',
        );
    }

    /**
     * @return \Spryker\Zed\AppMerchant\Business\AppMerchantFacadeInterface
     */
    protected function getFacade(): AppMerchantFacadeInterface
    {
        return new AppMerchantFacade();
    }
}
