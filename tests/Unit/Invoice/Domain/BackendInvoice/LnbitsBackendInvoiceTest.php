<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Invoice\Domain\BackendInvoice;

use PhpLightning\Invoice\Domain\BackendInvoice\LnbitsBackendInvoice;
use PhpLightning\Invoice\Domain\Http\HttpApiInterface;
use PhpLightning\Shared\Transfer\InvoiceTransfer;
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
        $expected = new InvoiceTransfer(error: 'Backend "LnBits" unreachable', status: 'ERROR');

        self::assertEquals($expected, $actual);
    }

    public function test_request_invoice_when_api_returns_payment_request(): void
    {
        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('postRequestInvoice')->willReturn([
            'bolt11' => 'ln1234567890',
            'status' => 'OK',
        ]);

        $invoice = new LnbitsBackendInvoice($httpApi, [
            'api_endpoint' => 'endpoint',
            'api_key' => 'key',
        ]);

        $actual = $invoice->requestInvoice(100);
        $expected = new InvoiceTransfer(bolt11: 'ln1234567890');

        self::assertEquals($expected, $actual);
    }
}
