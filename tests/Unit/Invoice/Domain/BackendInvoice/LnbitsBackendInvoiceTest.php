<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Invoice\Domain\BackendInvoice;

use PhpLightning\Invoice\Domain\BackendInvoice\LnbitsBackendInvoice;
use PhpLightning\Invoice\Domain\Http\HttpApiInterface;
use PhpLightning\Shared\Transfer\BackendInvoiceResponse;
use PHPUnit\Framework\TestCase;

final class LnbitsBackendInvoiceTest extends TestCase
{
    public function test_request_invoice_when_api_returns_null(): void
    {
        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('postRequestInvoice')->willReturn(null);

        $invoice = new LnbitsBackendInvoice($httpApi, [
            'api_endpoint' => 'endpoint',
            'api_key' => 'key',
        ]);
        $actual = $invoice->requestInvoice(100);

        $expected = (new BackendInvoiceResponse())
            ->setStatus('ERROR')
            ->setPaymentRequest('Backend "LnBits" unreachable');

        self::assertEquals($expected, $actual);
    }

    public function test_request_invoice_when_api_returns_no_payment_request(): void
    {
        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('postRequestInvoice')->willReturn([]);

        $invoice = new LnbitsBackendInvoice($httpApi, [
            'api_endpoint' => 'endpoint',
            'api_key' => 'key',
        ]);
        $actual = $invoice->requestInvoice(100);

        $expected = (new BackendInvoiceResponse())
            ->setStatus('ERROR')
            ->setPaymentRequest('No payment_request found');

        self::assertEquals($expected, $actual);
    }

    public function test_request_invoice_when_api_returns_payment_request(): void
    {
        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('postRequestInvoice')->willReturn([
            'payment_request' => 'ln1234567890',
        ]);

        $invoice = new LnbitsBackendInvoice($httpApi, [
            'api_endpoint' => 'endpoint',
            'api_key' => 'key',
        ]);
        $actual = $invoice->requestInvoice(100);

        $expected = (new BackendInvoiceResponse())
            ->setPaymentRequest('ln1234567890');

        self::assertEquals($expected, $actual);
    }
}
