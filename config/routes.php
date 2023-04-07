<?php

declare(strict_types=1);

use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('invoice', '/{username?}')
        ->controller(InvoiceController::class);
};
