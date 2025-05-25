<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractFacade;

/**
 * @method InvoiceFactory getFactory()
 */
final class InvoiceFacade extends AbstractFacade
{
    /**
     * @return array{
     *     callback: string,
     *     maxSendable: int,
     *     minSendable: int,
     *     metadata: string,
     *     tag: string,
     *     commentAllowed: bool,
     * }
     */
    public function getCallbackUrl(string $username): array
    {
        return $this->getFactory()
            ->createCallbackUrl($username)
            ->getCallbackUrl($username);
    }

    public function generateInvoice(string $username, int $milliSats): array
    {
        return $this->getFactory()
            ->createInvoiceGenerator($username)
            ->generateInvoice($milliSats);
    }
}
