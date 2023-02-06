<?php

declare(strict_types=1);

namespace PhpLightning\LnAddress;

use Gacela\Framework\AbstractDependencyProvider;
use Gacela\Framework\Container\Container;
use PhpLightning\Http\HttpFacade;
use PhpLightning\Invoice\InvoiceFacade;

final class LnAddressDependencyProvider extends AbstractDependencyProvider
{
    public const FACADE_INVOICE = 'FACADE_INVOICE';
    public const FACADE_HTTP = 'FACADE_HTTP';

    public function provideModuleDependencies(Container $container): void
    {
        $this->addInvoiceFacade($container);
        $this->addHttpFacade($container);
    }

    private function addInvoiceFacade(Container $container): void
    {
        $container->set(self::FACADE_INVOICE, static function (Container $container) {
            return $container->getLocator()->get(InvoiceFacade::class);
        });
    }

    private function addHttpFacade(Container $container): void
    {
        $container->set(self::FACADE_HTTP, static function (Container $container) {
            return $container->getLocator()->get(HttpFacade::class);
        });
    }
}
