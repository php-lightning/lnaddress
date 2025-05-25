<?php

declare(strict_types=1);

use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Router\Config\RouterGacelaConfig;
use PhpLightning\Invoice\Infrastructure\Plugin\InvoiceRoutesPlugin;

return static function (GacelaConfig $config): void {
    $config
        ->enableFileCache()
        ->addAppConfig('lightning-config.dist.php', 'lightning-config.php')
        ->extendGacelaConfig(RouterGacelaConfig::class)
        ->addPlugin(InvoiceRoutesPlugin::class);
};
