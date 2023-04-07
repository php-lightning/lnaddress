<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Http;

use PhpLightning\Invoice\Domain\Http\HttpApiInterface;
use Symfony\Component\HttpClient\HttpClient;

final class HttpApi implements HttpApiInterface
{
    public function postRequestInvoice(string $uri, string $body, array $headers = []): ?array
    {
        $response = HttpClient::create()
            ->request('POST', $uri, [
                'headers' => $headers,
                'body' => $body,
            ]);

        return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }
}
