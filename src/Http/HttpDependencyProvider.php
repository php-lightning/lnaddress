<?php

declare(strict_types=1);

namespace PhpLightning\Http;

use Gacela\Framework\AbstractDependencyProvider;
use Gacela\Framework\Container\Container;
use PhpLightning\Http\Infrastructure\FakeHttpClient;
use PhpLightning\Http\Infrastructure\SymfonyHttpClient;

/**
 * @method HttpConfig getConfig()
 */
final class HttpDependencyProvider extends AbstractDependencyProvider
{
    public const HTTP_CLIENT = 'HTTP_CLIENT';

    public function provideModuleDependencies(Container $container): void
    {
        $this->addHttpClient($container);
    }

    private function addHttpClient(Container $container): void
    {
        if ($this->getConfig()->isProd()) {
            $container->set(self::HTTP_CLIENT, static fn () => new SymfonyHttpClient());
        } else {
            $container->set(self::HTTP_CLIENT, static fn () => new FakeHttpClient());
        }
    }
}
