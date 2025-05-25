<?php

declare(strict_types=1);

namespace PhpLightning\Shared\Transfer;

use function array_key_exists;
use function is_array;

final class InvoiceTransfer
{
    public function __construct(
        public string $checkingId = '',
        public string $paymentHash = '',
        public string $walletId = '',
        public int $amount = 0,
        public int $fee = 0,
        public string $bolt11 = '',
        public string $status = 'OK',
        public string $memo = '',
        public ?string $expiry = null,
        public ?string $webhook = null,
        public ?string $webhookStatus = null,
        public ?string $preimage = null,
        public ?string $tag = null,
        public ?string $extension = null,
        public string $time = '',
        public string $createdAt = '',
        public string $updatedAt = '',
        public ?string $error = null,
        public ?InvoiceExtraTransfer $extra = null,
    ) {
    }

    public static function fromArray(array $array): self
    {
        return new self(
            checkingId: (string) ($array['checking_id'] ?? ''),
            paymentHash: (string) ($array['payment_hash'] ?? ''),
            walletId: (string) ($array['wallet_id'] ?? ''),
            amount: (int) ($array['amount'] ?? 0),
            fee: (int) ($array['fee'] ?? 0),
            bolt11: (string) ($array['bolt11'] ?? ''),
            status: (string) ($array['status'] ?? ''),
            memo: (string) ($array['memo'] ?? ''),
            expiry: array_key_exists('expiry', $array) ? (string) $array['expiry'] : null,
            webhook: array_key_exists('webhook', $array) ? (string) $array['webhook'] : null,
            webhookStatus: array_key_exists('webhook_status', $array) ? (string) $array['webhook_status'] : null,
            preimage: array_key_exists('preimage', $array) ? (string) $array['preimage'] : null,
            tag: array_key_exists('tag', $array) ? (string) $array['tag'] : null,
            extension: array_key_exists('extension', $array) ? (string) $array['extension'] : null,
            time: (string) ($array['time'] ?? ''),
            createdAt: (string) ($array['created_at'] ?? ''),
            updatedAt: (string) ($array['updated_at'] ?? ''),
            extra: isset($array['extra']) && is_array($array['extra'])
                ? InvoiceExtraTransfer::fromArray($array['extra'])
                : null,
        );
    }
}
