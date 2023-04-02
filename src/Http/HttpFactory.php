<?php

declare(strict_types=1);

namespace PhpLightning\Http;

use Gacela\Framework\AbstractFactory;
use PhpLightning\Http\Domain\FakeHttpApi;
use PhpLightning\Http\Domain\HttpApiInterface;
use PhpLightning\Http\Infrastructure\RealHttpApi;

/**
 * @method HttpConfig getConfig()
 */
final class HttpFactory extends AbstractFactory
{
    public function createHttpApi(): HttpApiInterface
    {
//        if ($this->getConfig()->isProd()) {
//            return new RealHttpApi(); # TODO: Use Symfony HttpClient instead
//        }

        return new FakeHttpApi();
    }
}
