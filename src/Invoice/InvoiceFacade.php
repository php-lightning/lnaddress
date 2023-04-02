<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractFacade;

/**
 * @method InvoiceFactory getFactory()
 */
final class InvoiceFacade extends AbstractFacade implements InvoiceFacadeInterface
{
    public function getCallbackUrl(): array
    {
        return $this->getFactory()
            ->createCallbackUrl()
            ->getCallbackUrl();
    }

    public function generate(int $milliSats, string $backend): array
    {
        return $this->getFactory()
            ->createInvoiceGenerator($backend)
            ->generateInvoice($milliSats, $backend);
    }
}
