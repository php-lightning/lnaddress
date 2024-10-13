<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractProvider;
use Gacela\Framework\Container\Container;
use PhpLightning\Invoice\Infrastructure\Http\HttpApi;

final class InvoiceDependencyProvider extends AbstractProvider
{
    public const HTTP_API = 'HTTP_API';

    public function provideModuleDependencies(Container $container): void
    {
        $container->set(self::HTTP_API, static fn () => new HttpApi());
    }
}
