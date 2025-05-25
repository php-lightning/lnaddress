<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

use PhpLightning\Shared\Transfer\InvoiceTransfer;

final readonly class EmptyBackendInvoice implements BackendInvoiceInterface
{
    public function __construct(private string $name)
    {
    }

    public function requestInvoice(int $satsAmount, string $metadata, string $memo = ''): InvoiceTransfer
    {
        return new InvoiceTransfer(status: 'ERROR', error: 'Unknown Backend: ' . $this->name);
    }
}
