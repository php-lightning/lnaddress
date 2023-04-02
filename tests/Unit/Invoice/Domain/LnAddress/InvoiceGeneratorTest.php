<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Invoice\Domain\LnAddress;

use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Invoice\Domain\LnAddress\InvoiceGenerator;
use PHPUnit\Framework\TestCase;

final class InvoiceGeneratorTest extends TestCase
{
    private const BACKEND = 'lnbits';

    public function test_unknown_backend(): void
    {
        $invoiceFacade = $this->createStub(BackendInvoiceInterface::class);
        $invoiceFacade->method('requestInvoice')->willReturn(['status' => 'ERROR', 'reason' => 'some reason']);

        $httpFacade = $this->createStub(HttpFacadeInterface::class);
        $httpFacade->method('get')->willReturn(null);

        $invoice = new InvoiceGenerator($invoiceFacade, $httpFacade, 'ln@address');
        $actual = $invoice->generateInvoice(123456, 'unknown?');

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

        $httpFacade = $this->createStub(HttpFacadeInterface::class);
        $httpFacade->method('get')->willReturn(
            json_encode([
                'payment_request' => 'any payment_request',
            ], JSON_THROW_ON_ERROR),
        );

        $invoice = new InvoiceGenerator($invoiceFacade, $httpFacade, 'ln@address');
        $actual = $invoice->generateInvoice(123456, self::BACKEND);

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

        $httpFacade = $this->createStub(HttpFacadeInterface::class);
        $httpFacade->method('get')->willReturn(null);

        $invoice = new InvoiceGenerator($invoiceFacade, $httpFacade, 'ln@address');
        $actual = $invoice->generateInvoice(100, self::BACKEND);

        self::assertSame([
            'status' => 'ERROR',
            'reason' => 'Amount is not between minimum and maximum sendable amount',
        ], $actual);
    }
}
