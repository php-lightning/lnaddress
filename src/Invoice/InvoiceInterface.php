<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

interface InvoiceInterface
{
    /**
     * @return array {
     *   status: string,
     *   reason: string,
     * }
     */
    public function requestInvoice(float $amount, string $metadata): array;
}
