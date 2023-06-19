<?php

declare(strict_types=1);

use Gacela\Framework\Gacela;
use Gacela\Router\Router;

$cwd = (string)getcwd();

require_once $cwd . '/vendor/autoload.php';

if (is_file($cwd . '/gacela.php')) {
    Gacela::bootstrap($cwd);
} else {
    Gacela::bootstrap(\dirname(__DIR__));
}

Gacela::get(Router::class)?->run();
