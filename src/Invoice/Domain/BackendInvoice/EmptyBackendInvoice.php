<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

final class EmptyBackendInvoice implements BackendInvoiceInterface
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return array {
     *   status: string,
     *   reason: string,
     * }
     */
    public function requestInvoice(float $amount, string $metadata): array
    {
        return [
            'status' => 'ERROR',
            'reason' => 'Unknown Backend: ' . $this->name,
        ];
    }
}
