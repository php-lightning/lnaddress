<?php

declare(strict_types=1);

namespace PhpLightning;

final class ServerConfig implements ConfigInterface
{
    public function getHttpHost(): string
    {
        return $_SERVER['HTTP_HOST'] ?? '';
    }

    public function getRequestUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '';
    }
}
