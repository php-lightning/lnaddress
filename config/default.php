<?php

declare(strict_types=1);

use PhpLightning\Invoice\InvoiceConfig;

return [
    'domain' => 'your-domain.com',
    'receiver' => 'custom-receiver',
    'min-sendable' => InvoiceConfig::DEFAULT_MIN_SENDABLE,
    'max-sendable' => InvoiceConfig::DEFAULT_MAX_SENDABLE,
    'backends' => [
        'lnbits' => [
            'api_endpoint' => 'http://localhost:5000',  // lnbits endpoint : protocol://host:port
            'api_key' => '',                            // put your lnbits read key here
        ],
    ],
];
