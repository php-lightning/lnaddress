<?php

declare(strict_types=1);

namespace PhpLightning\Config\Backend;

final class LnBitsBackendConfig implements BackendConfigInterface
{
    private string $apiEndpoint = 'http://localhost:5000';
    private string $apiKey = '';

    public static function withApiEndpointAndKey(string $endpoint, string $key): self
    {
        return (new self())
            ->setApiEndpoint($endpoint)
            ->setApiKey($key);
    }

    public function setApiEndpoint(string $apiEndpoint): self
    {
        $this->apiEndpoint = rtrim($apiEndpoint, '/');
        return $this;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return array{
     *     api_endpoint: string,
     *     api_key: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'api_endpoint' => $this->apiEndpoint,
            'api_key' => $this->apiKey,
        ];
    }
}
