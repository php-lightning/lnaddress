<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

interface BackendInvoiceInterface
{
    /**
     * @return array {
     *   status: string,
     *   reason: string,
     * }
     */
    public function requestInvoice(float $satsAmount, string $metadata): array;
}
