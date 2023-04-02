<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

interface InvoiceFacadeInterface
{
    /**
     * @return array {
     *   status: string,
     *   reason: string,
     * }
     */
    public function generate(int $amount, string $backend): array;
}
