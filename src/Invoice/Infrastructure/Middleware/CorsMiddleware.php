<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Middleware;

use Closure;
use Gacela\Router\Entities\Request;
use Gacela\Router\Middleware\MiddlewareInterface;

use function header;

final class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): string
    {
        // LNURL-pay endpoints are fetched cross-origin by wallets/browsers.
        header('Access-Control-Allow-Origin: *');

        if ($request->isMethod(Request::METHOD_OPTIONS)) {
            header('Access-Control-Allow-Methods: GET, OPTIONS');
            return '';
        }

        return $next($request);
    }
}
