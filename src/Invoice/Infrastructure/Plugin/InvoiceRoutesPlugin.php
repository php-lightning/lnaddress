<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Plugin;

use Gacela\Router\Configure\Routes;
use Gacela\Router\RouterInterface;
use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;

final class InvoiceRoutesPlugin
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    public function __invoke(): void
    {
        $this->router->configure(static function (Routes $routes): void {
            $routes->get('{username}', InvoiceController::class);
            $routes->get('/', InvoiceController::class);
        });
    }
}
