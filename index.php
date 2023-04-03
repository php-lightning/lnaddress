#!/usr/bin/env php
<?php

header("Access-Control-Allow-Origin: *");

use Gacela\Framework\Gacela;
use PhpLightning\Lightning;

$cwd = (string)getcwd();
if (!file_exists($autoloadPath = $cwd . '/vendor/autoload.php')) {
    exit("Cannot load composer's autoload file: " . $autoloadPath);
}

require $autoloadPath;

Gacela::bootstrap($cwd, Lightning::configFn());

// For now lnbits is the only backend supported
$backend = 'lnbits';

$milliSats = (int)($argv[1] ?? $_GET['amount'] ?? 0);

try {
    echo Lightning::generateInvoice($milliSats, $backend);
    echo PHP_EOL;
} catch (Throwable $e) {
    dd($e); // Intentional to have a better output error in case of exception
}
