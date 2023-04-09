<?php

declare(strict_types=1);

namespace PhpLightning\Router;

final class Request
{
    /**
     * @psalm-suppress RiskyCast
     */
    public static function getInt(string $key, int $default = 0): int
    {
        return (int)($_GET[$key] ?? $default);
    }
}
