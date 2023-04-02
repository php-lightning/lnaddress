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

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @return array<string,array>
     */
    public function jsonSerialize(): array
    {
        /**  @var array<string,array> $result */
        $result = [];

        foreach ($this->configs as $config) {
            /** @psalm-suppress MixedAssignment */
            $result[$config->getBackendName()] = $config->jsonSerialize();
        }

        return $result;
    }
}
