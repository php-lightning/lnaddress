<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

use PhpLightning\Shared\Transfer\BackendInvoiceResponse;

final readonly class EmptyBackendInvoice implements BackendInvoiceInterface
{
    public function __construct(private string $name)
    {
    }

    public function requestInvoice(int $satsAmount, string $metadata): BackendInvoiceResponse
    {
        return (new BackendInvoiceResponse())
            ->setStatus('ERROR')
            ->setReason('Unknown Backend: ' . $this->name);
    }
}
