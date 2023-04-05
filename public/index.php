<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

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
    if ($milliSats === 0) {
        echo Lightning::getCallbackUrl();
    } else {
        echo Lightning::generateInvoice($milliSats, $backend);
    }
} catch (Throwable $e) {
    echo $e->getMessage();
}
echo PHP_EOL;
