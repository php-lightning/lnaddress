<?php

declare(strict_types=1);

namespace PhpLightning;

interface ConfigInterface
{
    public function getHttpHost(): string;

    public function getRequestUri(): string;
}
