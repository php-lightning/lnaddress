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
            ->setApiEndpoint('http://localhost:5000') // lnbits endpoint : protocol://host:port
            ->setApiKey('3h9e75cf...9eca373'),        // put your lnbits read key here
    );
