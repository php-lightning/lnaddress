<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

interface InvoiceFacadeInterface
{
    /**
     * @return array{
     *     callback: string,
     *     maxSendable: string,
     *     minSendable: string,
     *     metadata: string,
     *     tag: string,
     *     commentAllowed: string,
     * }
     */
    public function getCallbackUrl(): array;

    /**
     * @return array {
     *   status: string,
     *   reason: string,
     * }
     */
    public function generateInvoice(int $milliSats, string $backend): array;
}
