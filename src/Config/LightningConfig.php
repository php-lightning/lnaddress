<?php

declare(strict_types=1);

namespace PhpLightning\Config;

use JsonSerializable;
use PhpLightning\Config\Backend\BackendConfigInterface;
use PhpLightning\Shared\Value\SendableRange;

final class LightningConfig implements JsonSerializable
{
    private ?string $domain = null;
    private ?string $receiver = null;
    private ?SendableRange $sendableRange = null;
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

    public function addBackend(BackendConfigInterface $backendConfig): self
    {
        $this->backends ??= new BackendsConfig();
        $this->backends->add($backendConfig);
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

        return $result;
    }
}
