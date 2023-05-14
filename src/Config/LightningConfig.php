<?php

declare(strict_types=1);

namespace PhpLightning\Config;

use JsonSerializable;
use PhpLightning\Config\Backend\BackendConfigInterface;
use PhpLightning\Config\Backend\LnBitsBackendConfig;
use PhpLightning\Shared\Value\SendableRange;
use RuntimeException;

final class LightningConfig implements JsonSerializable
{
    private ?string $domain = null;
    private ?string $receiver = null;
    private ?SendableRange $sendableRange = null;
    private ?string $callbackUrl = null;
    private ?BackendsConfig $backends = null;

    public function setDomain(string $domain): self
    {
        $parseUrl = parse_url($domain);
        $this->domain = $parseUrl['host'] ?? $domain;
        return $this;
    }

    public function setReceiver(string $receiver): self
    {
        $this->receiver = $receiver;
        return $this;
    }

    public function setSendableRange(int $min, int $max): self
    {
        $this->sendableRange = SendableRange::withMinMax($min, $max);
        return $this;
    }

    public function setCallbackUrl(string $callbackUrl): self
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function addBackendsFile(string $path): self
    {
        $jsonAsString = (string)file_get_contents($path);
        /** @var array<string, array{
         *     type: ?string,
         *     api_endpoint?: string,
         *     api_key?: string,
         * }> $json
         */
        $json = json_decode($jsonAsString, true);

        foreach ($json as $user => $settings) {
            if (!isset($settings['type'])) {
                throw new RuntimeException('"type" missing');
            }

            if ($settings['type'] === 'lnbits') { // TODO: refactor
                $this->addBackend(
                    $user,
                    LnBitsBackendConfig::withEndpointAndKey(
                        $settings['api_endpoint'] ?? '',
                        $settings['api_key'] ?? '',
                    ),
                );
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        $result = [];
        if ($this->backends !== null) {
            $result['backends'] = $this->backends->jsonSerialize();
        }
        if ($this->domain !== null) {
            $result['domain'] = $this->domain;
        }
        if ($this->receiver !== null) {
            $result['receiver'] = $this->receiver;
        }
        if ($this->sendableRange !== null) {
            $result['sendable-range'] = $this->sendableRange;
        }
        if ($this->callbackUrl !== null) {
            $result['callback-url'] = $this->callbackUrl;
        }

        return $result;
    }

    private function addBackend(string $username, BackendConfigInterface $backendConfig): self
    {
        $this->backends ??= new BackendsConfig();
        $this->backends->add($username, $backendConfig);
        return $this;
    }
}
