<?php

declare(strict_types=1);

namespace PhpLightning\LnAddress;

use Gacela\Framework\AbstractFactory;
use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\InvoiceFacadeInterface;
use PhpLightning\LnAddress\Domain\InvoiceGenerator;

/**
 * @method LnAddressConfig getConfig()
 */
final class LnAddressFactory extends AbstractFactory
{
    public function createInvoiceGenerator(): InvoiceGenerator
    {
        return new InvoiceGenerator(
            $this->getInvoiceFacade(),
            $this->getHttpFacade(),
            $this->getConfig()->getHttpHost(),
            $this->getConfig()->getCallback(),
        );
    }

    private function getInvoiceFacade(): InvoiceFacadeInterface
    {
        return $this->getProvidedDependency(LnAddressDependencyProvider::FACADE_INVOICE);
    }

    private function getHttpFacade(): HttpFacadeInterface
    {
        return $this->getProvidedDependency(LnAddressDependencyProvider::FACADE_HTTP);
    }
}
