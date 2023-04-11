<?php

declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

use Gacela\Framework\Gacela;
use Gacela\Router\Route;
use Gacela\Router\RoutingConfigurator;
use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;
use PhpLightning\Kernel;

require_once getcwd() . '/vendor/autoload.php';

Gacela::bootstrap(getcwd(), Kernel::gacelaConfigFn());

Route::configure(static function (RoutingConfigurator $routes): void {
    $routes->get('{username}/{amount}', InvoiceController::class);
    $routes->get('{username}', InvoiceController::class);
    $routes->get('/', InvoiceController::class);
});
