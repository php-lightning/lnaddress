<?php

declare(strict_types=1);

namespace PhpLightning;

final class FakeHttpApi implements HttpApiInterface
{
    /**
     * @param ?resource $context
     *
     * @return ?string null if occurred an error in the backend
     */
    public function get(string $uri, $context = null): ?string
    {
        return json_encode([
            '$uri' => $uri,
            '$context' => (string)$context,
        ]);
    }
}
