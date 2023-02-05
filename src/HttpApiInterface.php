<?php

declare(strict_types=1);

namespace PhpLightning;

interface HttpApiInterface
{
    /**
     * @param ?resource $context
     */
    public function get(string $uri, $context = null): string;
}
