<?php

declare(strict_types=1);

namespace PhpLightning\Http;

use Gacela\Framework\AbstractFactory;
use PhpLightning\Http\Domain\HttpApi;
use PhpLightning\Http\Domain\HttpApiInterface;
use PhpLightning\Http\Domain\HttpClientInterface;

/**
 * @method HttpConfig getConfig()
 */
final class HttpFactory extends AbstractFactory
{
    public function createHttpApi(): HttpApiInterface
    {
        return new HttpApi(
            $this->getHttpClient(),
        );
    }

    private function getHttpClient(): HttpClientInterface
    {
        return $this->getProvidedDependency(HttpDependencyProvider::HTTP_CLIENT);
    }
}
