<?php

declare(strict_types=1);

// This is the default config loaded, no need to have any APP_ENV env variable
// eg: php lnaddress.php

return [
    'mode' => 'test',
    'backends' => [
        'lnbits' => [
            'api_endpoint' => 'http://localhost:5000',  // lnbits endpoint : protocol://host:port
            'api_key' => '',                            // put your lnbits read key here
        ],
    ],
];
