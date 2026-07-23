<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Invoice\Infrastructure\Handler;

use PhpLightning\Invoice\Infrastructure\Handler\InvoiceExceptionHandler;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class InvoiceExceptionHandlerTest extends TestCase
{
    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function test_renders_exception_as_lnurl_error(): void
    {
        $json = (new InvoiceExceptionHandler())(new RuntimeException('Missing backend options for bob'));

        self::assertSame([
            'status' => 'ERROR',
            'reason' => 'Missing backend options for bob',
        ], json_decode($json, true, flags: JSON_THROW_ON_ERROR));
    }
}
