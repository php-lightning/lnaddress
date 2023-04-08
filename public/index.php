<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");

use Gacela\Framework\Gacela;
use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;
use PhpLightning\Kernel;

require_once getcwd() . '/vendor/autoload.php';
require_once __DIR__ . '/router.php';

Gacela::bootstrap(getcwd(), Kernel::gacelaConfigFn());

get('/', static function () {
    echo (new InvoiceController)->__invoke();
});

get('/$name', static function (string $name = '') {
    $amount = (int)($_GET['amount'] ?? 0);
    echo (new InvoiceController)->__invoke($name, $amount);
});
