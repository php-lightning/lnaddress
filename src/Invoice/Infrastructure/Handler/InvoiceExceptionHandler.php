<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Handler;

use Gacela\Router\Entities\JsonResponse;
use Throwable;

/**
 * Global router exception handler: turns any uncaught error into the LNURL
 * error object, so controllers don't each need a try/catch.
 */
final class InvoiceExceptionHandler
{
    public function __invoke(Throwable $exception): string
    {
        return (string)new JsonResponse([
            'status' => 'ERROR',
            'reason' => $exception->getMessage(),
        ]);
    }
}
