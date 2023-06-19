<?php

declare(strict_types=1);

use PhpLightning\Config\LightningConfig;

return (new LightningConfig())
    ->setDomain('localhost')
    ->setReceiver('default-receiver')
    ->setSendableRange(min: 100_000, max: 10_000_000_000)
    ->setCallbackUrl('localhost:8000/callback')
    ->addBackendsFile(getcwd() . DIRECTORY_SEPARATOR . 'nostr.json');
