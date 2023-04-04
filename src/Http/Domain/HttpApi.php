<?php

declare(strict_types=1);

namespace PhpLightning\Http\Domain;

final class HttpApi implements HttpApiInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @return ?string null if occurred an error in the backend
     */
    public function post(string $uri, array $options = []): ?string
    {
        $options = [
            explode(":", $uri)[0] => [ // extract protocol from $uri
                'header' => implode("\r\n", array_map(
                    function ($v, $k) { return sprintf("%s: %s", $k, $v); },
                    $options['headers'],
                    array_keys($options['headers'])
                )),
                'method' => 'POST',
                'content' => $options["body"]
            ]
        ];
        $context  = stream_context_create($options);
        return file_get_contents($uri, false, $context);
    }
}
