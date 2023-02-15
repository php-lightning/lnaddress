<?php

declare(strict_types=1);

namespace PhpLightning\Http;

use Gacela\Framework\AbstractConfig;

final class HttpConfig extends AbstractConfig
{
    public function isProd(): bool
    {
        return (string)$this->get('mode') === 'prod';
    }
}
