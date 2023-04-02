<?php

declare(strict_types=1);

namespace PhpLightning\Http;

use Gacela\Framework\AbstractFacade;

/**
 * @method HttpFactory getFactory()
 */
final class HttpFacade extends AbstractFacade implements HttpFacadeInterface
{
    /**
     * @return ?string null if occurred an error in the backend
     */
    public function post(string $uri, array $options = []): ?string
    {
        return $this->getFactory()
            ->createHttpApi()
            ->post($uri, $options);
    }
}
