<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\Http;

interface HttpApiInterface
{
    /**
     * @param array<string,string> $headers
     *
     * @return ?array{
     *     payment_hash?: string,
     *     payment_request?: string,
     *     checking_id?: string,
     *     lnurl_response: ?string,
     * } null if the endpoint is unreachable
     */
    public function postRequestInvoice(string $uri, string $body, array $headers = []): ?array;
}
