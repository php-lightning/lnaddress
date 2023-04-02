<?php

declare(strict_types=1);

namespace PhpLightning\Http\Infrastructure;

use PhpLightning\Http\Domain\HttpClientInterface;

final class FakeHttpClient implements HttpClientInterface
{
    public function post(string $url, array $options = []): string
    {
        return json_encode([
            '$uri' => $url,
            '$options' => $options,
        ], JSON_THROW_ON_ERROR);
    }
}
