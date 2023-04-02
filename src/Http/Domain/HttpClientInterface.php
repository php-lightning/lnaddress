<?php

declare(strict_types=1);

namespace PhpLightning\Http\Domain;

interface HttpClientInterface
{
    public function post(string $url, array $options = []): string;
}
