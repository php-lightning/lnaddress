<?php

declare(strict_types=1);

namespace PhpLightning\Invoice;

use PhpLightning\ConfigInterface;
use PhpLightning\HttpApiInterface;

final class BackendInvoiceFactory
{
    public const BACKEND_LNBITS = 'lnbits';

    private HttpApiInterface $httpApi;
    private ConfigInterface $config;

    public function __construct(HttpApiInterface $httpApi, ConfigInterface $config)
    {
        $this->httpApi = $httpApi;
        $this->config = $config;
    }

    public function createBackend(string $backend): InvoiceInterface
    {
        if ($backend === self::BACKEND_LNBITS) {
            return new LnBitsInvoice(
                $this->httpApi,
                $this->config->getBackendOptionsFor($backend),
            );
        }

        return new EmptyInvoice($backend);
    }
}
