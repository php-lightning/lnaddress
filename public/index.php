<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");

use Gacela\Framework\Gacela;
use Gacela\Router\Route;
use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;
use PhpLightning\Kernel;

require_once getcwd() . '/vendor/autoload.php';

Gacela::bootstrap(getcwd(), Kernel::gacelaConfigFn());

Route::get('{username}', InvoiceController::class);
Route::get('/', InvoiceController::class);
