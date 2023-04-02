<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractFactory;
use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Invoice\Domain\BackendInvoice\EmptyBackendInvoice;
use PhpLightning\Invoice\Domain\BackendInvoice\LnbitsBackendInvoice;
use PhpLightning\Invoice\Domain\CallbackUrl\CallbackUrl;
use PhpLightning\Invoice\Domain\CallbackUrl\CallbackUrlInterface;
use PhpLightning\Invoice\Domain\LnAddress\InvoiceGenerator;

/**
 * @method InvoiceConfig getConfig()
 */
final class InvoiceFactory extends AbstractFactory
{
    public function createCallbackUrl(): CallbackUrlInterface
    {
        return new CallbackUrl(
            $this->getHttpFacade(),
            $this->getConfig()->getLnAddress(),
            $this->getConfig()->getCallback(),
        );
    }

    public function createInvoiceGenerator(string $backend): InvoiceGenerator
    {
        return new InvoiceGenerator(
            $this->createBackend($backend),
            $this->getHttpFacade(),
            $this->getConfig()->getLnAddress(),
        );
    }

    private function createBackend(string $backend): BackendInvoiceInterface
    {
        return match ($backend) {
            'lnbits' => $this->createLnBitsBackendInvoice($backend),
            default => $this->createEmptyBackendInvoice($backend),
        };
    }

    private function createLnBitsBackendInvoice(string $backend): LnbitsBackendInvoice
    {
        return new LnbitsBackendInvoice(
            $this->getHttpFacade(),
            $this->getConfig()->getBackendOptionsFor($backend),
        );
    }

    private function createEmptyBackendInvoice(string $backend): EmptyBackendInvoice
    {
        return new EmptyBackendInvoice($backend);
    }

    private function getHttpFacade(): HttpFacadeInterface
    {
        return $this->getProvidedDependency(InvoiceDependencyProvider::FACADE_HTTP);
    }
}
