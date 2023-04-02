<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

interface BackendInvoiceInterface
{
    public const DEFAULT_BACKEND = 'lnbits';

    /**
     * @return array {
     *   status: string,
     *   reason: string,
     * }
     */
    public function requestInvoice(float $amount, string $metadata): array;
}
