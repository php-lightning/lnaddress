<?php

declare(strict_types=1);

use PhpLightning\Config\Backend\LnBitsBackendConfig;
use PhpLightning\Config\LightningConfig;

return (new LightningConfig())
    ->setDomain('your-domain.com')
    ->setReceiver('custom-receiver')
    ->setSendableRange(min: 100_000, max: 10_000_000_000)
    ->addBackend(
        (new LnBitsBackendConfig())
            ->setApiEndpoint('http://localhost:5000')
            ->setApiKey('api_key')
    );
