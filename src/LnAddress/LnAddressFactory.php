<?php

declare(strict_types=1);

namespace PhpLightning\LnAddress;

use Gacela\Framework\AbstractFactory;
use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\InvoiceFacadeInterface;
use PhpLightning\LnAddress\Domain\FileBaseNameLnAddressGenerator;
use PhpLightning\LnAddress\Domain\InvoiceGenerator;
use PhpLightning\LnAddress\Domain\LnAddressGeneratorInterface;

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
            $this->createLnAddressGenerator(),
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

    private function createLnAddressGenerator(): LnAddressGeneratorInterface
    {
        return new FileBaseNameLnAddressGenerator(
            $this->getConfig()->getHttpHost(),
        );
    }
}
