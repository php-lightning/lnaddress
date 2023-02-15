<?php

declare(strict_types=1);

namespace PhpLightningTest\LnAddress\Domain;

use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\InvoiceFacadeInterface;
use PhpLightning\LnAddress\Domain\InvoiceGenerator;
use PHPUnit\Framework\TestCase;

final class InvoiceGeneratorTest extends TestCase
{
    private const BACKEND = 'lnbits';
    private const HTTP_HOST = 'localhost';
    private const HTTP_CALLBACK = 'https://localhost/ping';

    public function test_unknown_backend(): void
    {
        $invoiceFacade = $this->createStub(InvoiceFacadeInterface::class);
        $invoiceFacade->method('requestInvoice')->willReturn(['status' => 'ERROR', 'reason' => 'some reason']);

        $httpFacade = $this->createStub(HttpFacadeInterface::class);
        $httpFacade->method('get')->willReturn(null);

        $lnAddress = new InvoiceGenerator($invoiceFacade, $httpFacade, self::HTTP_HOST, self::HTTP_CALLBACK);
        $actual = $lnAddress->generateInvoice(123456, 'unknown?');

        self::assertSame([
            'status' => 'ERROR',
            'reason' => 'some reason',
        ], $actual);
    }

    public function test_without_amount(): void
    {
        $invoiceFacade = $this->createStub(InvoiceFacadeInterface::class);
        $invoiceFacade->method('requestInvoice')->willReturn(['status' => 'OK']);

        $httpFacade = $this->createStub(HttpFacadeInterface::class);
        $httpFacade->method('get')->willReturn(null);

        $lnAddress = new InvoiceGenerator($invoiceFacade, $httpFacade, self::HTTP_HOST, self::HTTP_CALLBACK);

        $actual = $lnAddress->generateInvoice(0, self::BACKEND);

        self::assertSame([
            'callback' => 'https://localhost/ping',
            'maxSendable' => InvoiceGenerator::MAX_SENDABLE,
            'minSendable' => InvoiceGenerator::MIN_SENDABLE,
            'metadata' => json_encode([
                ['text/plain', 'Pay to InvoiceGenerator@localhost'],
                ['text/identifier', 'InvoiceGenerator@localhost'],
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            'tag' => 'payRequest',
            'commentAllowed' => 0,
        ], $actual);
    }

    public function test_successful_payment_request_with_amount(): void
    {
        $invoiceFacade = $this->createStub(InvoiceFacadeInterface::class);
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

        $lnAddress = new InvoiceGenerator($invoiceFacade, $httpFacade, self::HTTP_HOST, self::HTTP_CALLBACK);

        $actual = $lnAddress->generateInvoice(123456, self::BACKEND);

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
        $invoiceFacade = $this->createStub(InvoiceFacadeInterface::class);
        $invoiceFacade->method('requestInvoice')->willReturn(null);

        $httpFacade = $this->createStub(HttpFacadeInterface::class);
        $httpFacade->method('get')->willReturn(null);

        $lnAddress = new InvoiceGenerator($invoiceFacade, $httpFacade, self::HTTP_HOST, self::HTTP_CALLBACK);

        $actual = $lnAddress->generateInvoice(100, self::BACKEND);

        self::assertSame([
            'status' => 'ERROR',
            'reason' => 'Amount is not between minimum and maximum sendable amount',
        ], $actual);
    }
}
