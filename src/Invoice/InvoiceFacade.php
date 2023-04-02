<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractFacade;

/**
 * @method InvoiceFactory getFactory()
 */
final class InvoiceFacade extends AbstractFacade implements InvoiceFacadeInterface
{
    public function generate(int $amount, string $backend = InvoiceFactory::BACKEND_LNBITS): array
    {
        return $this->getFactory()
            ->createInvoiceGenerator($backend)
            ->generateInvoice($amount, $backend);
    }
}
