<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractFacade;

/**
 * @method InvoiceFactory getFactory()
 */
final class InvoiceFacade extends AbstractFacade implements InvoiceFacadeInterface
{
    /**
     * @return array {
     *   status: string,
     *   reason: string,
     * }
     */
    public function requestInvoice(string $backend, float $amount, string $metadata)
    {
        return $this->getFactory()
            ->createBackend($backend)
            ->requestInvoice($amount, $metadata);
    }
}
