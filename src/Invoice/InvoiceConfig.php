<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractConfig;
use PhpLightning\Shared\Config\ConfigKey;
use PhpLightning\Shared\Value\SendableRange;
use RuntimeException;

use function sprintf;

final class InvoiceConfig extends AbstractConfig
{
    public function getCallback(): string
    {
        return (string)$this->get(ConfigKey::CALLBACK_URL, 'undefined:callback-url');
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
        return (array)$this->get(ConfigKey::BACKENDS); // @phpstan-ignore-line
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
        return $this->get(ConfigKey::SENDABLE_RANGE, SendableRange::default());
    }

    public function getDescriptionTemplate(): string
    {
        return (string)$this->get(ConfigKey::DESCRIPTION_TEMPLATE, 'Pay to %s');
    }

    public function getSuccessMessage(): string
    {
        return (string)$this->get(ConfigKey::SUCCESS_MESSAGE, 'Payment received!');
    }

    public function getInvoiceMemo(): string
    {
        return (string)$this->get(ConfigKey::INVOICE_MEMO, '');
    }

    public function getDomain(): string
    {
        return (string)$this->get(ConfigKey::DOMAIN, $_SERVER['HTTP_HOST'] ?? 'localhost');
    }

    private function getReceiver(): string
    {
        return (string)$this->get(ConfigKey::RECEIVER, 'unknown-receiver');
    }
}
