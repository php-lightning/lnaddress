<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

final class EmptyInvoice implements InvoiceInterface
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
