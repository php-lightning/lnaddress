<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractConfig;
use PhpLightning\Invoice\Domain\Transfer\SendableRange;
use RuntimeException;

final class InvoiceConfig extends AbstractConfig
{
    /** @var int 100 Minimum in msat (sat/1000) */
    public const DEFAULT_MIN_SENDABLE = 100_000;

    /** @var int 10 000 000 Max in msat (sat/1000) */
    public const DEFAULT_MAX_SENDABLE = 10_000_000_000;

    public function getCallback(): string
    {
        return sprintf('https://%s/%s', $this->getDomain(), $this->getReceiver());
    }

    public function getLnAddress(): string
    {
        return sprintf('%s@%s', $this->getReceiver(), $this->getDomain());
    }

    /**
     * @return array{
     *     api_endpoint: string,
     *     api_key: string,
     * }
     */
    public function getBackendOptionsFor(string $backend): array
    {
        /** @var  array{api_endpoint?: string, api_key?: string} $result */
        $result = $this->get('backends')[$backend] ?? []; // @phpstan-ignore-line

        if (!isset($result['api_endpoint'], $result['api_key'])) {
            throw new RuntimeException('Missing backend options for ' . $backend);
        }

        return $result;
    }

    public function getSendableRange(): SendableRange
    {
        return SendableRange::withMinMax($this->getMinSendable(), $this->getMaxSendable());
    }

    private function getMinSendable(): int
    {
        return (int)$this->get('min-sendable', self::DEFAULT_MIN_SENDABLE);
    }

    private function getMaxSendable(): int
    {
        return (int)$this->get('max-sendable', self::DEFAULT_MAX_SENDABLE);
    }

    private function getDomain(): string
    {
        return (string)$this->get('domain', $_SERVER['HTTP_HOST'] ?? 'localhost');
    }

    private function getReceiver(): string
    {
        return (string)$this->get('receiver', $_SERVER['REQUEST_URI'] ?? 'unknown-receiver');
    }
}
