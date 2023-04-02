<?php

declare(strict_types=1);

return [
    'mode' => 'test',
    'domain' => 'your-domain.com',
    'receiver' => 'custom-receiver',
    'backends' => [
        'lnbits' => [
            'api_endpoint' => 'http://localhost:5000',  // lnbits endpoint : protocol://host:port
            'api_key' => '',                            // put your lnbits read key here
        ],
    ],
];
