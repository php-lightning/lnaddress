<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");

use Gacela\Framework\Gacela;
use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;
use PhpLightning\Kernel;
use PhpLightning\Router\Router;

require_once getcwd() . '/vendor/autoload.php';

Gacela::bootstrap(getcwd(), Kernel::gacelaConfigFn());

Router::get('/', InvoiceController::class);
Router::get('{username}', InvoiceController::class);
