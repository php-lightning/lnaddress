<?php

declare(strict_types=1);

namespace PhpLightning\LnAddress;

use Gacela\Framework\AbstractConfig;

final class LnAddressConfig extends AbstractConfig
{
    public function getCallback(): string
    {
        return 'https://' . $this->getHttpHost() . $this->getRequestUri();
    }

    public function getHttpHost(): string
    {
        return $_SERVER['HTTP_HOST'] ?? 'localhost';
    }

    private function getRequestUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/ping';
    }
}
