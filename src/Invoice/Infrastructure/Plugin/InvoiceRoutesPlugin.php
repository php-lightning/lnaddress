<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Plugin;

use Exception;
use Gacela\Router\Configure\Handlers;
use Gacela\Router\Configure\Middlewares;
use Gacela\Router\Configure\Routes;
use Gacela\Router\Entities\Request;
use Gacela\Router\RouterInterface;
use PhpLightning\Invoice\Infrastructure\Controller\InvoiceController;
use PhpLightning\Invoice\Infrastructure\Handler\InvoiceExceptionHandler;
use PhpLightning\Invoice\Infrastructure\Middleware\CorsMiddleware;

final readonly class InvoiceRoutesPlugin
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    public function __invoke(): void
    {
        $this->router->configure(
            static function (Routes $routes, Middlewares $middlewares, Handlers $handlers): void {
                // OPTIONS is registered so CORS preflight reaches CorsMiddleware,
                // which short-circuits it before InvoiceController runs.
                $routes->match([Request::METHOD_GET, Request::METHOD_OPTIONS], '{username?}', InvoiceController::class);
                $middlewares->add(new CorsMiddleware());
                $handlers->handle(Exception::class, new InvoiceExceptionHandler());
            },
        );
    }
}
