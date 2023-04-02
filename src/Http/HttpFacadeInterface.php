<?php

declare(strict_types=1);

namespace PhpLightning\Http;

interface HttpFacadeInterface
{
    /**
     * @return ?string null if occurred an error in the backend
     */
    public function post(string $uri, array $options = []): ?string;
}
