<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Invoice\Infrastructure\Middleware;

use Gacela\Router\Entities\Request;
use PhpLightning\Invoice\Infrastructure\Middleware\CorsMiddleware;
use PHPUnit\Framework\TestCase;

final class CorsMiddlewareTest extends TestCase
{
    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function test_options_preflight_short_circuits_without_calling_next(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $nextCalled = false;

        $result = (new CorsMiddleware())->handle(
            Request::fromGlobals(),
            static function () use (&$nextCalled): string {
                $nextCalled = true;
                return 'NEXT';
            },
        );

        self::assertSame('', $result);
        self::assertFalse($nextCalled);
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function test_get_request_delegates_to_next(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $result = (new CorsMiddleware())->handle(
            Request::fromGlobals(),
            static fn (): string => 'NEXT',
        );

        self::assertSame('NEXT', $result);
    }
}
