<?php

declare(strict_types=1);

namespace PhpLightning;

final class HttpApi implements HttpApiInterface
{
    /**
     * @param ?resource $context
     *
     * @return ?string null if occurred an error in the backend
     */
    public function get(string $uri, $context = null): ?string
    {
        if ($context === null) {
            return file_get_contents($uri) ?: null;
        }

        return file_get_contents($uri, false, $context) ?: null;
    }
}
