<?php

declare(strict_types=1);

use Gacela\Framework\Gacela;
use PhpLightning\Kernel;

require_once getcwd() . '/vendor/autoload_runtime.php';

return static function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool)$context['APP_DEBUG']);

    Gacela::bootstrap(getcwd(), $kernel->gacelaConfigFn());

    return $kernel;
};

