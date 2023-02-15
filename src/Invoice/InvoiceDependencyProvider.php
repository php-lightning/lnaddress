<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractDependencyProvider;
use Gacela\Framework\Container\Container;
use PhpLightning\Http\HttpFacade;

final class InvoiceDependencyProvider extends AbstractDependencyProvider
{
    public const FACADE_HTTP = 'FACADE_HTTP';

    public function provideModuleDependencies(Container $container): void
    {
        $this->addHttpFacade($container);
    }

    private function addHttpFacade(Container $container): void
    {
        $container->set(self::FACADE_HTTP, static function (Container $container) {
            return $container->getLocator()->get(HttpFacade::class);
        });
    }
}
