<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Invoice\Domain\CallbackUrl;

use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Invoice\Domain\CallbackUrl\CallbackUrl;
use PhpLightning\Shared\Transfer\BackendInvoiceResponse;
use PhpLightning\Shared\Value\SendableRange;
use PHPUnit\Framework\TestCase;

final class CallbackUrlTest extends TestCase
{
    public function test_get_callback_url(): void
    {
        $invoiceFacade = $this->createStub(BackendInvoiceInterface::class);
        $invoiceFacade->method('requestInvoice')->willReturn(new BackendInvoiceResponse());

        $callbackUrl = new CallbackUrl(
            SendableRange::withMinMax(1_000, 5_000),
            'ln@address',
            'https://domain/receiver',
        );

        self::assertEquals([
            'callback' => 'https://domain/receiver',
            'minSendable' => 1_000,
            'maxSendable' => 5_000,
            'metadata' => json_encode([
                ['text/plain', 'Pay to ln@address'],
                ['text/identifier', 'ln@address'],
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            'tag' => 'payRequest',
            'commentAllowed' => false,
        ], $callbackUrl->getCallbackUrl('username'));
    }
}
