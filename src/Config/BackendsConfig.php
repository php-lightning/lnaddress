<?php

declare(strict_types=1);

namespace PhpLightning\Config;

use JsonSerializable;
use PhpLightning\Config\Backend\BackendConfigInterface;

final class BackendsConfig implements JsonSerializable
{
    /** @var list<BackendConfigInterface> */
    private array $configs = [];

    public function add(BackendConfigInterface $backendConfig): self
    {
        $this->configs[] = $backendConfig;
        return $this;
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->configs as $config) {
            $result[$config->getBackendName()] = $config->jsonSerialize();
        }

        return $result;
    }
}
