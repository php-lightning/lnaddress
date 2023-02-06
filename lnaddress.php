<?php

use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\Gacela;
use PhpLightning\Lightning;

$cwd = (string)getcwd();
if (!file_exists($autoloadPath = $cwd . '/vendor/autoload.php')) {
    exit("Cannot load composer's autoload file: " . $autoloadPath);
}

require $autoloadPath;

Gacela::bootstrap(__DIR__, GacelaConfig::withPhpConfigDefault());

// @see `config/default.php` to change the api_endpoint and api_key
// Backend settings, for now lnbits is the only backend supported
$backend = 'lnbits';

$amount = $argv[1] ?? $_GET['amount'] ?? 0;
$amount = filter_var($amount, FILTER_VALIDATE_INT);

$invoice = Lightning::generateInvoice($amount, $backend);

echo json_encode($invoice, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . PHP_EOL;
