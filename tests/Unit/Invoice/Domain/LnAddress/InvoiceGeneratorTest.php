<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Invoice\Domain\LnAddress;

use PhpLightning\Invoice\Application\InvoiceGenerator;
use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Shared\Transfer\InvoiceTransfer;
use PhpLightning\Shared\Value\SendableRange;
use PHPUnit\Framework\TestCase;

final class InvoiceGeneratorTest extends TestCase
{
    public function test_invalid_amount(): void
    {
        $invoiceFacade = $this->createStub(BackendInvoiceInterface::class);
        $invoiceFacade->method('requestInvoice')->willReturn(new InvoiceTransfer());

        $invoice = new InvoiceGenerator(
            $invoiceFacade,
            SendableRange::withMinMax(1_000, 3_000),
            'ln@address',
            'Pay to %s',
            'Payment received!'
        );
        $actual = $invoice->generateInvoice(100);

        self::assertSame([
            'status' => 'ERROR',
            'reason' => 'Amount is not between minimum and maximum sendable amount',
        ], $actual);
    }

    public function test_unknown_backend(): void
    {
        $invoiceFacade = $this->createStub(BackendInvoiceInterface::class);
        $invoiceFacade->method('requestInvoice')
            ->willReturn(new InvoiceTransfer(error: 'some reason', status: 'ERROR'));

        $invoice = new InvoiceGenerator(
            $invoiceFacade,
            SendableRange::withMinMax(1_000, 3_000),
            'ln@address',
            'Pay to %s',
            'Payment received!'
        );
        $actual = $invoice->generateInvoice(2_000);

        self::assertEquals([
            'bolt11' => '',
            'status' => 'ERROR',
            'memo' => '',
            'successAction' => [
                'tag' => 'message',
                'message' => 'Payment received!',
            ],
            'routes' => [],
            'disposable' => false,
            'error' => 'some reason',
        ], $actual);
    }

    public function test_successful_payment_request_with_amount(): void
    {
        $invoiceFacade = $this->createStub(BackendInvoiceInterface::class);
        $invoiceFacade->method('requestInvoice')
            ->willReturn(new InvoiceTransfer(bolt11: 'ln123456789', memo: 'Custom memo'));

        $invoice = new InvoiceGenerator(
            $invoiceFacade,
            SendableRange::withMinMax(1_000, 3_000),
            'ln@address',
            'Pay to %s',
            'Payment received!'
        );
        $actual = $invoice->generateInvoice(2_000);

        self::assertEquals([
            'bolt11' => 'ln123456789',
            'status' => 'OK',
            'memo' => 'Custom memo',
            'successAction' => [
                'tag' => 'message',
                'message' => 'Payment received!',
            ],
            'routes' => [],
            'disposable' => false,
            'error' => null,
        ], $actual);
    }

    public function test_passes_description_as_memo_to_backend(): void
    {
        $backend = $this->createMock(BackendInvoiceInterface::class);
        $backend->expects(self::once())
            ->method('requestInvoice')
            ->with(2, $this->anything(), 'Pay to ln@address')
            ->willReturn(new InvoiceTransfer());

        $invoice = new InvoiceGenerator(
            $backend,
            SendableRange::withMinMax(1_000, 3_000),
            'ln@address',
            'Pay to %s',
            'Payment received!'
        );

        $invoice->generateInvoice(2_000);
    }
}
