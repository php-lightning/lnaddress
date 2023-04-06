<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

use PhpLightning\Shared\Transfer\BackendInvoiceResponse;

final class EmptyBackendInvoice implements BackendInvoiceInterface
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function requestInvoice(int $satsAmount, string $metadata): BackendInvoiceResponse
    {
        return (new BackendInvoiceResponse())
            ->setStatus('ERROR')
            ->setReason('Unknown Backend: ' . $this->name);
    }
}
