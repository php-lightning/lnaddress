<?php

declare(strict_types=1);

namespace PhpLightning\LnAddress;

use Gacela\Framework\AbstractFacade;
use PhpLightning\Invoice\InvoiceFactory;

/**
 * @method LnAddressFactory getFactory()
 */
final class LnAddressFacade extends AbstractFacade
{
    public function generateInvoice(
        int $amount,
        string $backend = InvoiceFactory::BACKEND_LNBITS,
        string $imageFile = '',
    ): array {
        return $this->getFactory()
            ->createInvoiceGenerator()
            ->generateInvoice($amount, $backend, $imageFile);
    }
}
