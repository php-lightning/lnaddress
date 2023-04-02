<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use Gacela\Framework\AbstractConfig;
use RuntimeException;

final class InvoiceConfig extends AbstractConfig
{
    public function getCallback(): string
    {
        return 'https://' . $this->getHttpHost() . $this->getRequestUri();
    }

    public function getHttpHost(): string
    {
        return $_SERVER['HTTP_HOST'] ?? 'localhost';
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

    private function getRequestUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/ping';
    }
}
