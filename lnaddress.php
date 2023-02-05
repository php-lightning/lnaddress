<?php

use PhpLightning\HttpApi;
use PhpLightning\LnAddress;
use PhpLightning\ServerConfig;

$cwd = (string)getcwd();
if (!file_exists($autoloadPath = $cwd . '/vendor/autoload.php')) {
    exit("Cannot load composer's autoload file: " . $autoloadPath);
}

require $autoloadPath;

header("Content-Type: application/json");

// @see ServerConfig::getAllBackendOptions() to change the api_endpoint and api_key
// Backend settings, for now lnbits is the only backend supported, please set api_endpoint & api_key below
$backend = 'lnbits';

$amount = filter_var($_GET['amount'] ?? 0, FILTER_VALIDATE_INT);

$lnAddress = new LnAddress(
    new HttpApi(),
    new ServerConfig()
);

$invoice = $lnAddress->generateInvoice($amount, $backend);

echo json_encode($invoice, JSON_THROW_ON_ERROR);
