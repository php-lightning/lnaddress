<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractDependencyProvider;
use Gacela\Framework\Container\Container;
use PhpLightning\Invoice\Infrastructure\Http\HttpApi;

final class InvoiceDependencyProvider extends AbstractDependencyProvider
{
    public const HTTP_API = 'HTTP_API';

    public function provideModuleDependencies(Container $container): void
    {
        $container->set(self::HTTP_API, static fn () => new HttpApi());
    }
}
