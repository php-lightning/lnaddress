<?php

declare(strict_types=1);

namespace PhpLightning\Shared\Transfer;

final class InvoiceExtraTransfer
{
    public function __construct(
        public string $walletFiatCurrency,
        public float $walletFiatAmount,
        public float $walletFiatRate,
        public float $walletBtcRate,
    ) {
    }

    public static function fromArray(array $array): self
    {
        return new self(
            walletFiatCurrency: (string) $array['wallet_fiat_currency'],
            walletFiatAmount: (float) $array['wallet_fiat_amount'],
            walletFiatRate: (float) $array['wallet_fiat_rate'],
            walletBtcRate: (float) $array['wallet_btc_rate'],
        );
    }
}
