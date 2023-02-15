<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractFactory;
use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\Domain\EmptyInvoice;
use PhpLightning\Invoice\Domain\InvoiceInterface;
use PhpLightning\Invoice\Domain\LnbitsInvoice;

/**
 * @method InvoiceConfig getConfig()
 */
final class InvoiceFactory extends AbstractFactory
{
    public const BACKEND_LNBITS = 'lnbits';

    public function createBackend(string $backend): InvoiceInterface
    {
        if ($backend === self::BACKEND_LNBITS) {
            return new LnbitsInvoice(
                $this->getHttpFacade(),
                $this->getConfig()->getBackendOptionsFor($backend),
            );
        }

        return new EmptyInvoice($backend);
    }

    private function getHttpFacade(): HttpFacadeInterface
    {
        return $this->getProvidedDependency(InvoiceDependencyProvider::FACADE_HTTP);
    }
}
