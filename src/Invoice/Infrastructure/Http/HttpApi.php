<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Http;

use JsonException;
use PhpLightning\Invoice\Domain\Http\HttpApiInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

final class HttpApi implements HttpApiInterface
{
    public function postRequestInvoice(string $uri, string $body, array $headers = []): ?array
    {
        try {
            $response = HttpClient::create()
                ->request('POST', $uri, [
                    'headers' => $headers,
                    'body' => $body,
                ]);

            // getContent() throws on transport failure and on 3xx/4xx/5xx, json_decode
            // on a malformed 200 body; null lets the backend surface a clean error
            // instead of a raw stack trace.
            return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (ExceptionInterface|JsonException) {
            return null;
        }
    }
}
