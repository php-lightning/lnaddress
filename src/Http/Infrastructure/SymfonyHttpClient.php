<?php

declare(strict_types=1);

namespace PhpLightning\Http\Infrastructure;

use PhpLightning\Http\Domain\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;

final class SymfonyHttpClient implements HttpClientInterface
{
    public function post(string $url, array $options = []): string
    {
        return HttpClient::create()
            ->request('POST', $url, $options)
            ->getContent();
    }
}
