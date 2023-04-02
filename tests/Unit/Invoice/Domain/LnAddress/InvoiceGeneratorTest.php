<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Invoice\Domain\LnAddress;

use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Invoice\Domain\LnAddress\InvoiceGenerator;
use PhpLightning\Invoice\Domain\Transfer\SendableRange;
use PHPUnit\Framework\TestCase;

final class InvoiceGeneratorTest extends TestCase
{
    private const BACKEND = 'lnbits';

    public function test_unknown_backend(): void
    {
        $invoiceFacade = $this->createStub(BackendInvoiceInterface::class);
        $invoiceFacade->method('requestInvoice')->willReturn(['status' => 'ERROR', 'reason' => 'some reason']);

        $invoice = new InvoiceGenerator(
            $invoiceFacade,
            SendableRange::withMinMax(1_000, 3_000),
            'ln@address',
        );
        $actual = $invoice->generateInvoice(2_000, 'unknown?');

        self::assertSame([
            'status' => 'ERROR',
            'reason' => 'some reason',
        ], $actual);
    }

    public function test_successful_payment_request_with_amount(): void
    {
        $invoiceFacade = $this->createStub(BackendInvoiceInterface::class);
        $invoiceFacade->method('requestInvoice')->willReturn([
            'status' => 'OK',
            'pr' => 'any payment_request',
        ]);

        $invoice = new InvoiceGenerator(
            $invoiceFacade,
            SendableRange::withMinMax(1_000, 3_000),
            'ln@address',
        );
        $actual = $invoice->generateInvoice(2_000, self::BACKEND);

        self::assertSame([
            'pr' => 'any payment_request',
            'status' => 'OK',
            'successAction' => [
                'tag' => 'message',
                'message' => 'Payment received!',
            ],
            'routes' => [],
            'disposable' => false,
        ], $actual);
    }

    public function test_invalid_amount(): void
    {
        $invoiceFacade = $this->createStub(BackendInvoiceInterface::class);
        $invoiceFacade->method('requestInvoice')->willReturn([]);

        $invoice = new InvoiceGenerator(
            $invoiceFacade,
            SendableRange::withMinMax(1_000, 3_000),
            'ln@address',
        );
        $actual = $invoice->generateInvoice(100, self::BACKEND);

        self::assertSame([
            'status' => 'ERROR',
            'reason' => 'Amount is not between minimum and maximum sendable amount',
        ], $actual);
    }
}
