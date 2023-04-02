<?php

declare(strict_types=1);

return [
    'domain' => 'your-domain.com',
    'receiver' => 'custom-receiver',
//    'min-sendable' => 100_000,
//    'max-sendable' => 10_000_000_000,
    'backends' => [
        'lnbits' => [
            'api_endpoint' => 'http://localhost:5000',  // lnbits endpoint : protocol://host:port
            'api_key' => '',                            // put your lnbits read key here
        ],
    ],
];
