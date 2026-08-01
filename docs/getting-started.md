# Getting started

## Requirements

- PHP >= 8.3
- Composer 2
- An LNbits wallet (self-hosted or hosted) — you need its API endpoint and API key

## Install

### Standalone

```bash
git clone https://github.com/php-lightning/lnaddress.git
cd lnaddress
composer install
```

The `post-install-cmd` script copies `backends.dist.json` to `backends.json` when the
latter does not exist yet.

### As a dependency

```bash
composer require php-lightning/lnaddress
```

The [demo template](https://github.com/php-lightning/demo-lnaddress) is a ready-made
project built on top of this library: it keeps your config and pulls features and fixes
with `composer update`.

## Configure

Two files, both gitignored so your keys never reach version control:

```bash
cp lightning-config.dist.php lightning-config.php
cp backends.dist.json backends.json   # already done by composer install
```

**`backends.json`** — one entry per username you serve:

```json
{
  "bob": {
    "type": "lnbits",
    "api_key": "abc...123",
    "api_endpoint": "https://legend.lnbits.com"
  }
}
```

**`lightning-config.php`** — everything else:

```php
<?php

use PhpLightning\Config\LightningConfig;

return (new LightningConfig())
    ->setDomain('example.com')
    ->setReceiver('bob')
    ->setDescriptionTemplate('Pay to %s')
    ->setSuccessMessage('Thanks for the payment!')
    ->setInvoiceMemo('')
    ->setSendableRange(min: 100_000, max: 10_000_000_000)
    ->setCallbackUrl('https://example.com')
    ->addBackendsFile(getcwd() . DIRECTORY_SEPARATOR . 'backends.json');
```

`lightning-config.php` overrides `lightning-config.dist.php` key by key. Full reference in
[Configuration](configuration.md).

## Run locally

```bash
composer serve   # php -S localhost:8080 public/index.php
```

```bash
curl 'http://localhost:8080/bob'
curl 'http://localhost:8080/bob?amount=2000'
```

> The `callback` URL is what paying wallets call in step 2 of the LNURL-pay flow, so it
> must match the host you actually serve — `http://localhost:8080` while testing locally.

## Go live

Wallets resolve `bob@example.com` by fetching:

```
https://example.com/.well-known/lnurlp/bob
```

Serve `public/index.php` on that domain over HTTPS and rewrite
`/.well-known/lnurlp/{username}` to `/{username}`. Keep `setDomain()` and
`setCallbackUrl()` in sync with the public URL. Details and an nginx snippet in
[HTTP API](api.md).
