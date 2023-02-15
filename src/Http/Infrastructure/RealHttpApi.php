<?php

declare(strict_types=1);

namespace PhpLightning\Http\Infrastructure;

use PhpLightning\Http\Domain\HttpApiInterface;

final class RealHttpApi implements HttpApiInterface
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
