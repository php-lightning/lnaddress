<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

use PhpLightning\Shared\Transfer\BackendInvoiceResponse;

interface BackendInvoiceInterface
{
    public function requestInvoice(int $satsAmount, string $metadata): BackendInvoiceResponse;
}
