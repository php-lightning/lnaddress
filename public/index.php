<?php

declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

use Gacela\Framework\Gacela;
use Gacela\Router\Router;
use Gacela\Router\Routes;
use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;
use PhpLightning\Kernel;

$cwd = (string)getcwd();

require_once $cwd . '/vendor/autoload.php';

Gacela::bootstrap($cwd, Kernel::gacelaConfigFn());

Router::configure(static function (Routes $routes): void {
    $routes->get('{username}/{amount}', InvoiceController::class);
    $routes->get('{username}', InvoiceController::class);
    $routes->get('/', InvoiceController::class);
});
