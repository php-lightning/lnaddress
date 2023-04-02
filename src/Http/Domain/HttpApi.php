<?php

declare(strict_types=1);

namespace PhpLightning\Http\Domain;

final class HttpApi implements HttpApiInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @return ?string null if occurred an error in the backend
     */
    public function post(string $uri, array $options = []): ?string
    {
        return $this->httpClient->post($uri, $options);
    }
}
