<?php

declare(strict_types=1);

use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\Gacela;
use Gacela\Router\Router;
use Gacela\Router\Routes;
use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;

$cwd = (string)getcwd();

require_once $cwd . '/vendor/autoload.php';

Gacela::bootstrap($cwd, static function (GacelaConfig $config): void {
    $config->addAppConfig('lightning-config.dist.php', 'lightning-config.php');
    $config->setFileCache(true);
});

$router = new Router(static function (Routes $routes): void {
    $routes->get('{username}/{amount}', InvoiceController::class);
    $routes->get('{username}', InvoiceController::class);
    $routes->get('/', InvoiceController::class);
});

$router->run();
