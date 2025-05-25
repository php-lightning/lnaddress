<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractConfig;
use PhpLightning\Shared\Value\SendableRange;
use RuntimeException;

use function sprintf;

final class InvoiceConfig extends AbstractConfig
{
    public function getCallback(): string
    {
        return (string)$this->get('callback-url', 'undefined:callback-url');
    }

    public function getDefaultLnAddress(): string
    {
        return sprintf('%s@%s', $this->getReceiver(), $this->getDomain());
    }

    /**
     * @return array<string,array>
     */
    public function getBackends(): array
    {
        /** @psalm-suppress MixedReturnTypeCoercion */
        return (array)$this->get('backends'); // @phpstan-ignore-line
    }

    /**
     * @return array{
     *     api_endpoint: string,
     *     api_key: string,
     * }
     */
    public function getBackendOptionsFor(string $username): array
    {
        /** @var  array{api_endpoint?: string, api_key?: string} $result */
        $result = $this->getBackends()[$username] ?? [];

        if (!isset($result['api_endpoint'], $result['api_key'])) {
            throw new RuntimeException('Missing backend options for ' . $username);
        }

        return $result;
    }

    public function getSendableRange(): SendableRange
    {
        return $this->get('sendable-range', SendableRange::default());
    }

    public function getDescriptionTemplate(): string
    {
        return (string)$this->get('description-template', 'Pay to %s');
    }

    public function getSuccessMessage(): string
    {
        return (string)$this->get('success-message', 'Payment received!');
    }

    public function getInvoiceMemo(): string
    {
        return (string)$this->get('invoice-memo', '');
    }

    public function getDomain(): string
    {
        return (string)$this->get('domain', $_SERVER['HTTP_HOST'] ?? 'localhost');
    }

    private function getReceiver(): string
    {
        return (string)$this->get('receiver', $_SERVER['REQUEST_URI'] ?? 'unknown-receiver');
    }
}
