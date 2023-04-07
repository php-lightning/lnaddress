<?php

declare(strict_types=1);

namespace PhpLightning\Config;

use JsonSerializable;
use PhpLightning\Config\Backend\BackendConfigInterface;

final class BackendsConfig implements JsonSerializable
{
    /** @var array<string, BackendConfigInterface> */
    private array $configs = [];

    public function add(string $username, BackendConfigInterface $backendConfig): self
    {
        $this->configs[$username] = $backendConfig;
        return $this;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @return array<string,array>
     */
    public function jsonSerialize(): array
    {
        /**  @var array<string,array> $result */
        $result = [];

        foreach ($this->configs as $username => $config) {
            /** @psalm-suppress MixedAssignment */
            $result[$username] = $config->jsonSerialize();
        }

        return $result;
    }
}
