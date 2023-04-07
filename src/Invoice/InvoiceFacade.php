<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractFacade;

/**
 * @method InvoiceFactory getFactory()
 */
final class InvoiceFacade extends AbstractFacade
{
    public function getCallbackUrl(string $username): array
    {
        return $this->getFactory()
            ->createCallbackUrl()
            ->getCallbackUrl($username);
    }

    public function generateInvoice(string $username, int $milliSats): array
    {
        return $this->getFactory()
            ->createInvoiceGenerator($username)
            ->generateInvoice($milliSats);
    }
}
