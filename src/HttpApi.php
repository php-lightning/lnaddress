<?php

declare(strict_types=1);

namespace PhpLightning;

final class HttpApi implements HttpApiInterface
{
    /**
     * @param ?resource $context
     */
    public function get(string $uri, $context = null): string
    {
        if ($context === null) {
            return file_get_contents($uri);
        }

        return file_get_contents($uri, false, $context);
    }
}
