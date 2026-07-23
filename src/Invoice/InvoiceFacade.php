<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractFacade;

/**
 * @extends AbstractFacade<InvoiceFactory>
 *
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

    /**
     * @return array{
     *     pr: string,
     *     status: string,
     *     memo: string,
     *     successAction: array{tag: string, message: string},
     *     routes: list<mixed>,
     *     disposable: bool,
     *     error: string|null,
     * }|array{status: string, reason: string}
     */
    public function generateInvoice(string $username, int $milliSats): array
    {
        return $this->getFactory()
            ->createInvoiceGenerator($username)
            ->generateInvoice($milliSats);
    }
}
