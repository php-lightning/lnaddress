<?php

declare(strict_types=1);

namespace PhpLightning\Config\Backend;

final class LnBitsBackendConfig implements BackendConfigInterface
{
    private string $apiEndpoint = 'http://localhost:5000';
    private string $apiKey = '';

    public function setApiEndpoint(string $apiEndpoint): self
    {
        $this->apiEndpoint = $apiEndpoint;
        return $this;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getBackendName(): string
    {
        return 'lnbits';
    }

    public function jsonSerialize(): array
    {
        return [
            // lnbits endpoint : protocol://host:port
            'api_endpoint' => $this->apiEndpoint,
            'api_key' => $this->apiKey,
        ];
    }
}
