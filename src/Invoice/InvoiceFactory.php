<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractFactory;
use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Invoice\Domain\BackendInvoice\EmptyBackendInvoice;
use PhpLightning\Invoice\Domain\BackendInvoice\LnbitsBackendInvoice;
use PhpLightning\Invoice\Domain\LnAddress\FileBaseNameLnAddressGenerator;
use PhpLightning\Invoice\Domain\LnAddress\InvoiceGenerator;
use PhpLightning\Invoice\Domain\LnAddress\LnAddressGeneratorInterface;

/**
 * @method InvoiceConfig getConfig()
 */
final class InvoiceFactory extends AbstractFactory
{
    public const BACKEND_LNBITS = 'lnbits';

    public function createInvoiceGenerator(string $backend): InvoiceGenerator
    {
        return new InvoiceGenerator(
            $this->createBackend($backend),
            $this->getHttpFacade(),
            $this->createLnAddressGenerator(),
            $this->getConfig()->getCallback(),
        );
    }

    private function createLnAddressGenerator(): LnAddressGeneratorInterface
    {
        return new FileBaseNameLnAddressGenerator(
            $this->getConfig()->getHttpHost(),
        );
    }

    private function createBackend(string $backend): BackendInvoiceInterface
    {
        if ($backend === self::BACKEND_LNBITS) {
            return new LnbitsBackendInvoice(
                $this->getHttpFacade(),
                $this->getConfig()->getBackendOptionsFor($backend),
            );
        }

        return new EmptyBackendInvoice($backend);
    }

    private function getHttpFacade(): HttpFacadeInterface
    {
        return $this->getProvidedDependency(InvoiceDependencyProvider::FACADE_HTTP);
    }
}
