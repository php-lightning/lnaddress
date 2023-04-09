<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");

use Gacela\Framework\Gacela;
use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;
use PhpLightning\Kernel;
use PhpLightning\Router\Router;

require_once getcwd() . '/vendor/autoload.php';

Gacela::bootstrap(getcwd(), Kernel::gacelaConfigFn());

$router = Router::withServer($_SERVER);

$router->get('/', InvoiceController::class);
$router->get('/$username', InvoiceController::class);

$router->listen();
