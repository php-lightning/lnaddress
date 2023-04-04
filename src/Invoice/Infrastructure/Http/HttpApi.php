<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Http;

use PhpLightning\Invoice\Domain\Http\HttpApiInterface;

final class HttpApi implements HttpApiInterface
{
    public function postRequestInvoice(string $uri, string $body, array $headers = []): ?array
    {
        // TODO: Consider using something like this:
        //        HttpClient::create()
        //            ->request('POST', $url, $options)
        //            ->getContent();
        $options = [
            explode(':', $uri)[0] => [ // extract protocol from $uri
                'header' => implode(
                    "\r\n",
                    array_map(
                        static fn (string $v, string $k) => sprintf('%s: %s', $k, $v),
                        $headers,
                        array_keys($headers),
                    ),
                ),
                'method' => 'POST',
                'content' => $body,
            ],
        ];

        $context = stream_context_create($options);
        $response = (string)file_get_contents($uri, false, $context);

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
