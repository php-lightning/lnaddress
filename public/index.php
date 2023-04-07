<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

use Gacela\Framework\Gacela;
use PhpLightning\Config\Backend\LnBitsBackendConfig;
use PhpLightning\Config\LightningConfig;
use PhpLightning\Lightning;

$cwd = (string)getcwd();
if (!file_exists($autoloadPath = $cwd . '/vendor/autoload.php')) {
    exit("Cannot load composer's autoload file: " . $autoloadPath);
}

require $autoloadPath;

$lightningConfig = (new LightningConfig())
    ->setDomain($_SERVER['HTTP_HOST'] ?? '')
    ->setReceiver(str_replace('.php', '', basename(__FILE__)))
    ->setSendableRange(100_000, 10_000_000_000)
    ->setCallbackUrl('https://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])
    ->addBackend(
        (new LnBitsBackendConfig())
            ->setApiEndpoint('http://localhost:5000')       // paste your lnbits endpoint here
            ->setApiKey('Invoice LNbits API key goes here') // paste your lnbits invoice key here
    );

Gacela::bootstrap($cwd, Lightning::configFn($lightningConfig));


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
