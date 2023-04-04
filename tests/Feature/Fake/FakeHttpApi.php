<?php

declare(strict_types=1);

namespace PhpLightningTest\Feature\Fake;

use PhpLightning\Invoice\Domain\Http\HttpApiInterface;

final class FakeHttpApi implements HttpApiInterface
{
    public function postRequestInvoice(string $uri, string $body, array $headers = []): ?array
    {
        return [
            'payment_hash' => 'fake payment_hash',
            'payment_request' => 'lnbc10u1pjzh489...fake payment_request',
            'checking_id' => 'fake checking_id',
            'lnurl_response' => 'fake lnurl_response',
        ];
    }
}
