<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Glue\Testify\Helper\GlueApplication\Bootstrap;

use Spryker\Glue\GlueApplication\Bootstrap\GlueBootstrap;
use Spryker\Glue\GlueBackendApiApplication\Plugin\GlueApplication\BackendApiGlueApplicationBootstrapPlugin;
use Spryker\Shared\Application\ApplicationInterface;

class GlueBackendApiBootstrap extends GlueBootstrap
{
    /**
     * @param array<string> $glueApplicationBootstrapPluginClassNames
     */
    public function boot(array $glueApplicationBootstrapPluginClassNames = []): ApplicationInterface
    {
        return parent::boot([BackendApiGlueApplicationBootstrapPlugin::class]);
    }
}
