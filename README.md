# PHP Lightning Address

<p align="center">
  <a href="https://github.com/php-lightning/lnaddress/actions">
    <img src="https://github.com/php-lightning/lnaddress/workflows/CI/badge.svg" alt="GitHub Build Status">
  </a>
  <a href="https://scrutinizer-ci.com/g/php-lightning/lnaddress/?branch=main">
    <img src="https://scrutinizer-ci.com/g/php-lightning/lnaddress/badges/quality-score.png?b=main" alt="Scrutinizer Code Quality">
  </a>
  <a href="https://scrutinizer-ci.com/g/php-lightning/lnaddress/?branch=main">
    <img src="https://scrutinizer-ci.com/g/php-lightning/lnaddress/badges/coverage.png?b=main" alt="Scrutinizer Code Coverage">
  </a>
  <a href="https://shepherd.dev/github/php-lightning/lnaddress">
    <img src="https://shepherd.dev/github/php-lightning/lnaddress/coverage.svg" alt="Psalm Type-coverage Status">
  </a>
  <a href="https://github.com/php-lightning/lnaddress/blob/master/LICENSE">
    <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="MIT Software License">
  </a>
</p>

Self-host your own [Lightning Address](https://lightningaddress.com) in PHP: a human-readable identifier like `you@yourdomain.com` that any Lightning wallet can pay. It implements [LNURL-pay (LUD-06)](https://github.com/lnurl/luds/blob/luds/06.md) and is backend-agnostic — [LNbits](https://lnbits.com) is the backend available today. Built on the [Gacela](https://gacela-project.com) framework.

## Requirements

- PHP >= 8.2

## Install

```bash
composer require php-lightning/lnaddress
```

`composer install` runs a post-install step that copies `backends.dist.json` → `backends.json` if the latter does not exist yet.

Prefer starting from a working project? Use the ready-made [demo template](https://github.com/php-lightning/demo-lnaddress). It depends on this library, so a `composer update` pulls in new features and fixes as they land here.

## Configure

There are two config files: `lightning-config.php` (settings) and `backends.json` (per-user invoice backends).

### 1. Settings — `lightning-config.php`

```bash
cp lightning-config.dist.php lightning-config.php
```

`LightningConfig` has a fluent API:

```php
use PhpLightning\Config\LightningConfig;

return (new LightningConfig())
    ->setDomain('yourdomain.com')
    ->setReceiver('default-receiver')
    ->setDescriptionTemplate('Pay to %s')   // %s = the lightning address
    ->setSuccessMessage('Thanks for the payment!')
    ->setInvoiceMemo('')
    ->setSendableRange(min: 100_000, max: 10_000_000_000) // millisats
    ->setCallbackUrl('https://yourdomain.com')
    ->addBackendsFile(getcwd() . '/backends.json');
```

### 2. Backends — `backends.json`

```bash
cp backends.dist.json backends.json
```

Each username maps to its own invoice backend:

```json
{
  "bob":   { "type": "lnbits", "api_key": "abc...123", "api_endpoint": "http://localhost:5000" },
  "alice": { "type": "lnbits", "api_key": "def...456", "api_endpoint": "http://localhost:5000" }
}
```

### Register backends programmatically (no JSON file)

You can skip `backends.json` and register backends directly in `lightning-config.php`:

```php
use PhpLightning\Config\Backend\LnBitsBackendConfig;

$config->addBackend('bob', LnBitsBackendConfig::withEndpointAndKey('http://localhost:5000', 'abc...123'));
```

## Run the server

```bash
composer serve
```

This starts `php -S localhost:8080 public/index.php`.

## HTTP API

One route serves the full LNURL-pay flow: `GET /{username?}`. The username is optional — when omitted, the request resolves to the default `receiver@domain` from your config.

Every response carries permissive CORS headers (`Access-Control-Allow-Origin: *`) so browser-based wallets can call it, and `OPTIONS` preflight requests are answered directly. Uncaught errors are turned into the LNURL error object by a global handler.

### Step 1 — pay params

`GET /bob` (no `amount`) returns the LNURL-pay parameters:

```json
{
  "callback": "https://yourdomain.com",
  "maxSendable": 10000000000,
  "minSendable": 100000,
  "metadata": "[[\"text/plain\",\"Pay to bob@yourdomain.com\"],[\"text/identifier\",\"bob@yourdomain.com\"]]",
  "tag": "payRequest",
  "commentAllowed": false
}
```

### Step 2 — invoice

`GET /bob?amount=<millisats>` returns a bolt11 invoice for that amount:

```json
{
  "pr": "lnbc20n1p...",
  "status": "OK",
  "memo": "",
  "successAction": { "tag": "message", "message": "Thanks for the payment!" },
  "routes": [],
  "disposable": false,
  "error": null
}
```

### Errors

Failures return an LNURL error object, for example when the amount falls outside the sendable range or the backend is unreachable:

```json
{ "status": "ERROR", "reason": "Amount is not between minimum and maximum sendable amount" }
```

> **Units:** the sendable range and the `amount` query param are in **millisats**. The backend is billed in **sats** (`millisats / 1000`).

## Use as a library (programmatic)

You can call the facade directly instead of going over HTTP:

```php
use Gacela\Framework\Gacela;
use PhpLightning\Invoice\InvoiceFacade;

Gacela::bootstrap(__DIR__);

$facade = new InvoiceFacade();
$payParams = $facade->getCallbackUrl('bob');           // LNURL-pay params
$invoice   = $facade->generateInvoice('bob', 2_000);   // millisats
```

## Configuration reference

| Setter | Purpose | Default |
| --- | --- | --- |
| `setDomain(string)` | Your domain (URL scheme is stripped) | — |
| `setReceiver(string)` | Default username when none is in the URL | — |
| `setSendableRange(int $min, int $max)` | Allowed amounts, in millisats | `100_000` – `10_000_000_000` |
| `setCallbackUrl(string)` | Public callback base URL wallets call back to | — |
| `setDescriptionTemplate(string)` | LNURL metadata description (`%s` = the address) | `Pay to %s` |
| `setSuccessMessage(string)` | Message shown after a successful payment | `Payment received!` |
| `setInvoiceMemo(string)` | Memo attached to the invoice | `''` |
| `addBackendsFile(string $path)` / `addBackend(string $username, ...)` | Register invoice backends | — |

## Adding a new backend

Backends are keyed by a `type` string, resolved through the `PhpLightning\Config\Backend\BackendType` enum. To add one:

- Add a case to `PhpLightning\Config\Backend\BackendType`.
- Handle that case in `LightningConfig::createBackendConfig()`.
- Implement `PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface`.

## Development / Testing

```bash
composer test-all      # quality + phpunit + rector (dry-run)
```

Other useful scripts:

- `composer test-phpunit` — run the PHPUnit suite
- `composer quality` — php-cs-fixer (dry-run), psalm, phpstan
- `composer fix` — php-cs-fixer + rector (apply fixes)

See [.github/CONTRIBUTING.md](.github/CONTRIBUTING.md) before opening a PR.

## Wiki

More details in the [wiki](https://github.com/php-lightning/lnaddress/wiki).

## Contributions

Issues and pull requests are welcome. Licensed under [MIT](LICENSE).
