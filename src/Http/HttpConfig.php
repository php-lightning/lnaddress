<?php

declare(strict_types=1);

namespace PhpLightning\Http;

use Gacela\Framework\AbstractConfig;

final class HttpConfig extends AbstractConfig
{
    public function isProd(): bool
    {
        dump($this->get('mode'));
        return $this->get('mode') === 'prod';
    }
}
