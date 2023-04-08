<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");

use Gacela\Framework\Gacela;
use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;
use PhpLightning\Kernel;
use PhpLightning\Router\Router;

require_once getcwd() . '/vendor/autoload.php';

Gacela::bootstrap(getcwd(), Kernel::gacelaConfigFn());

$router = new Router(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

$router->get('/', static function () {
    echo (new InvoiceController)->__invoke();
});

$router->get('/$name', static function (string $name = '') {
    $amount = (int)($_GET['amount'] ?? 0);
    echo (new InvoiceController)->__invoke($name, $amount);
});
