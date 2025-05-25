<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractFactory;
use PhpLightning\Invoice\Application\CallbackUrl;
use PhpLightning\Invoice\Application\InvoiceGenerator;
use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Invoice\Domain\BackendInvoice\LnbitsBackendInvoice;
use PhpLightning\Invoice\Domain\CallbackUrl\CallbackUrlInterface;
use PhpLightning\Invoice\Domain\CallbackUrl\LnAddressGenerator;
use PhpLightning\Invoice\Domain\CallbackUrl\LnAddressGeneratorInterface;
use PhpLightning\Invoice\Domain\Http\HttpApiInterface;

/**
 * @method InvoiceConfig getConfig()
 */
final class InvoiceFactory extends AbstractFactory
{
    public function createCallbackUrl(string $username): CallbackUrlInterface
    {
        if ($username !== '') {
            $this->validateUserExists($username);
        }

        return new CallbackUrl(
            $this->getConfig()->getSendableRange(),
            $this->createLnAddressGenerator(),
            $this->getConfig()->getCallback(),
            $this->getConfig()->getDescriptionTemplate(),
        );
    }

    public function createInvoiceGenerator(string $username): InvoiceGenerator
    {
        return new InvoiceGenerator(
            $this->getBackendForUser($username),
            $this->getConfig()->getSendableRange(),
            $this->getConfig()->getDefaultLnAddress(),
            $this->getConfig()->getDescriptionTemplate(),
            $this->getConfig()->getSuccessMessage(),
        );
    }

    private function createLnAddressGenerator(): LnAddressGeneratorInterface
    {
        return new LnAddressGenerator(
            $this->getConfig()->getDefaultLnAddress(),
            $this->getConfig()->getDomain(),
        );
    }

    private function getBackendForUser(string $username): BackendInvoiceInterface
    {
        return new LnbitsBackendInvoice(
            $this->getHttpApi(),
            $this->getConfig()->getBackendOptionsFor($username),
        );
    }

    private function getHttpApi(): HttpApiInterface
    {
        return $this->getProvidedDependency(InvoiceDependencyProvider::HTTP_API);
    }

    private function validateUserExists(string $username): void
    {
        $this->getConfig()->getBackendOptionsFor($username);
    }
}
