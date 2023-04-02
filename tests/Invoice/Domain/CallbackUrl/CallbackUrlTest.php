<?php

declare(strict_types=1);

namespace PhpLightningTest\Invoice\Domain\CallbackUrl;

use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Invoice\Domain\CallbackUrl\CallbackUrl;
use PhpLightning\Invoice\Domain\LnAddress\InvoiceGenerator;
use PHPUnit\Framework\TestCase;

final class CallbackUrlTest extends TestCase
{
    public function test_get_callback_url(): void
    {
        $invoiceFacade = $this->createStub(BackendInvoiceInterface::class);
        $invoiceFacade->method('requestInvoice')->willReturn(['status' => 'OK']);

        $httpFacade = $this->createStub(HttpFacadeInterface::class);

        $callbackUrl = new CallbackUrl($httpFacade, 'ln@address', 'https://domain/receiver');

        self::assertSame([
            'callback' => 'https://domain/receiver',
            'maxSendable' => InvoiceGenerator::MAX_SENDABLE,
            'minSendable' => InvoiceGenerator::MIN_SENDABLE,
            'metadata' => json_encode([
                ['text/plain', 'Pay to ln@address'],
                ['text/identifier', 'ln@address'],
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            'tag' => 'payRequest',
            'commentAllowed' => false,
        ], $callbackUrl->getCallbackUrl());
    }
}
