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

// Backend settings, for now lnbits is the only backend supported, please set api_endpoint & api_key below
$backend = 'lnbits';
$backend_options = [];
$backend_options['lnbits'] = [
    'api_endpoint' => 'http://localhost:5000',  // lnbits endpoint : protocol://host:port
    'api_key' => ''                             // put your lnbits read key here
];

$amount = filter_var($_GET['amount'] ?? 0, FILTER_VALIDATE_INT);

$lnAddress = new LnAddress(
    new HttpApi(),
    new ServerConfig()
);

$lnAddress->generateInvoice($amount, $backend, $backend_options);
