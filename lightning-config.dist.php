<?php

declare(strict_types=1);

use PhpLightning\Config\LightningConfig;

return (new LightningConfig())
    ->setDomain('localhost')
    ->setReceiver('default-receiver')
    // %s is replaced with the payer-facing lightning address
    ->setDescriptionTemplate('Pay to %s')
    ->setSuccessMessage('Payment received!')
    ->setInvoiceMemo('')
    // min/max are in millisats (sat * 1000)
    ->setSendableRange(min: 100_000, max: 10_000_000_000)
    // public URL wallets call back to request the invoice
    ->setCallbackUrl('http://localhost:8080')
    ->addBackendsFile(getcwd() . DIRECTORY_SEPARATOR . 'backends.json');
