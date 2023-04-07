<?php

declare(strict_types=1);

use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\Gacela;
use PhpLightning\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool)$context['APP_DEBUG']);

    $configFn = static function (GacelaConfig $config) use ($kernel): void {
        $config->addAppConfig('lightning-config.dist.php', 'lightning-config.php');
        $config->setFileCacheEnabled(true);
        $config->setFileCacheDirectory($kernel->getProjectDir() . '/data/.cache');
    };

    Gacela::bootstrap($kernel->getProjectDir(), $configFn);

    return $kernel;
};
