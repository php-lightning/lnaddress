<?php

declare(strict_types=1);

use PhpLightning\Config\Backend\LnBitsBackendConfig;
use PhpLightning\Config\LightningConfig;

return (new LightningConfig())
    ->setDomain('localhost')
    ->setReceiver('default-receiver')
    ->setSendableRange(min: 100_000, max: 10_000_000_000)
    ->setCallbackUrl('localhost:8000/callback')

    #################################################
    # You can add backend configurations in two ways
    #################################################

    # OPTION A) using an external json
    ->addBackendsAsJson(__DIR__ . DIRECTORY_SEPARATOR . 'nostr.json')

    # OPTION B) using custom PHP config
    ->setBackends([
        'user-1' => LnBitsBackendConfig::withEndpointAndKey('http://localhost:5000', 'api_key-1'),
        'user-2' => LnBitsBackendConfig::withEndpointAndKey('http://localhost:5000', 'api_key-2'),
    ])
;
