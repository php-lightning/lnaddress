<?php

declare(strict_types=1);

namespace PhpLightning;

use RuntimeException;

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

    /**
     * @return array{
     *     api_endpoint: string,
     *     api_key: string,
     * }
     */
    public function getBackendOptionsFor(string $backend): array
    {
        $result = $this->getAllBackendOptions()[$backend] ?? [];

        if (!isset($result['api_endpoint'], $result['api_key'])) {
            throw new RuntimeException('Missing backend options for ' . $backend);
        }

        return $result;
    }

    /**
     * @return array<string,array{
     *     api_endpoint?: string,
     *     api_key?: string,
     * }>
     */
    private function getAllBackendOptions(): array
    {
        return [
            'lnbits' => [
                'api_endpoint' => 'http://localhost:5000',
                'api_key' => '',
            ],
            // ...
        ];
    }
}
