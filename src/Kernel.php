<?php

declare(strict_types=1);

namespace PhpLightning;

use Gacela\Framework\Bootstrap\GacelaConfig;

final class Kernel
{
    /**
     * @return callable(GacelaConfig):void
     */
    public static function gacelaConfigFn(): callable
    {
        return static function (GacelaConfig $config): void {
            $config->addAppConfig('lightning-config.dist.php', 'lightning-config.php');
            $config->setFileCacheEnabled(true);
        };
    }
}
