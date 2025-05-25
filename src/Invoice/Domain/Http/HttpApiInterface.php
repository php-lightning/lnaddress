<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\Http;

interface HttpApiInterface
{
    /**
     * @param  array<string, string>  $headers
     *
     * @return array{
     *     checking_id: string,
     *     payment_hash: string,
     *     wallet_id: string,
     *     amount: int,
     *     fee: int,
     *     bolt11: string,
     *     status: string,
     *     memo: string,
     *     expiry: string|null,
     *     webhook: string|null,
     *     webhook_status: string|null,
     *     preimage: string|null,
     *     tag: string|null,
     *     extension: string|null,
     *     time: string,
     *     created_at: string,
     *     updated_at: string,
     *     extra: array{
     *         wallet_fiat_currency: string,
     *         wallet_fiat_amount: float,
     *         wallet_fiat_rate: float,
     *         wallet_btc_rate: float
     *     }
     * }|null
     */
    public function postRequestInvoice(string $uri, string $body, array $headers = []): ?array;
}
