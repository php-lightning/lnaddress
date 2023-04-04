<?php

declare(strict_types=1);

namespace PhpLightning\Config;

use JsonSerializable;
use PhpLightning\Config\Backend\BackendConfigInterface;

final class LightningConfig implements JsonSerializable
{
    private string $mode = 'test';
    private ?string $domain = null;
    private ?string $receiver = null;
    private ?int $minSendable = null;
    private ?int $maxSendable = null;
    private BackendsConfig $backends;

    public function __construct()
    {
        $this->backends = new BackendsConfig();
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

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

    public function setMinSendable(int $minSendable): self
    {
        $this->minSendable = $minSendable;
        return $this;
    }

    public function setMaxSendable(int $maxSendable): self
    {
        $this->maxSendable = $maxSendable;
        return $this;
    }

    public function addBackend(BackendConfigInterface $backendConfig): self
    {
        $this->backends->add($backendConfig);
        return $this;
    }

    public function jsonSerialize(): array
    {
        $result = [
            'mode' => $this->mode,
            'backends' => $this->backends->jsonSerialize(),
        ];

        if ($this->domain !== null) {
            $result['domain'] = $this->domain;
        }
        if ($this->receiver !== null) {
            $result['receiver'] = $this->receiver;
        }
        if ($this->minSendable !== null) {
            $result['min-sendable'] = $this->minSendable;
        }
        if ($this->maxSendable !== null) {
            $result['max-sendable'] = $this->maxSendable;
        }

        return $result;
    }
}
