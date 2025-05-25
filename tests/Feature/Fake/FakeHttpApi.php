<?php

declare(strict_types=1);

namespace PhpLightningTest\Feature\Fake;

use PhpLightning\Invoice\Domain\Http\HttpApiInterface;

final class FakeHttpApi implements HttpApiInterface
{
    public function postRequestInvoice(string $uri, string $body, array $headers = []): ?array
    {
        return [
            'checking_id' => '8efa8998bca6e298ae63dc7425c8a34b5373511d88a70f6f29ba98f833a63f04',
            'payment_hash' => '8efa8998bca6e298ae63dc7425c8a34b5373511d88a70f6f29ba98f833a63f04',
            'wallet_id' => 'd73709a1301146b7ae0c748e3a0ecef2',
            'amount' => 1000000,
            'fee' => 0,
            'bolt11' => 'lnbc10u1p5r9lmwpp53magnx9u5m3f3tnrm36ztj9rfdfhx5ga3zns7mefh2v0svax8uzqcqzyssp54twf429a8cvz6tflw5lt705gfnvuykhdeewey009tugjcuamt38q9q7sqqqqqqqqqqqqqqqqqqqsqqqqqysgqdqqmqz9gxqrrssrzjqwryaup9lh50kkranzgcdnn2fgvx390wgj5jd07rwr3vxeje0glclll4ttz7sp6kpvqqqqlgqqqqqeqqjq0uu89sejjllry5ye43x0v42jn48c6alfc9mfnjla2u6kmwy444pzrjmtu25nk2shshuh2mrqtehygmzya9xg89ppszuuhd9296vvcxspkpwc68',
            'status' => 'pending',
            'memo' => '',
            'expiry' => '2025-05-25T12:30:54',
            'webhook' => null,
            'webhook_status' => null,
            'preimage' => null,
            'tag' => null,
            'extension' => null,
            'time' => '2025-05-25T11:30:54.433442+00:00',
            'created_at' => '2025-05-25T11:30:54.433447+00:00',
            'updated_at' => '2025-05-25T11:30:54.433449+00:00',
            'extra' => [
                'wallet_fiat_currency' => 'USD',
                'wallet_fiat_amount' => 1.071,
                'wallet_fiat_rate' => 933.31818946794,
                'wallet_btc_rate' => 107144.595625,
            ],
        ];
    }
}
