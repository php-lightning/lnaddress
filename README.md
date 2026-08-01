# PHP Lightning Address

<p align="center">
  <a href="https://github.com/php-lightning/lnaddress/actions">
    <img src="https://github.com/php-lightning/lnaddress/workflows/CI/badge.svg" alt="GitHub Build Status">
  </a>
  <a href="https://shepherd.dev/github/php-lightning/lnaddress">
    <img src="https://shepherd.dev/github/php-lightning/lnaddress/coverage.svg" alt="Psalm Type-coverage Status">
  </a>
  <a href="https://github.com/php-lightning/lnaddress/blob/main/LICENSE">
    <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="MIT Software License">
  </a>
</p>

Self-host your own [Lightning Address](https://lightningaddress.com) in PHP: a
human-readable identifier like `you@yourdomain.com` that any Lightning wallet can pay. It
implements [LNURL-pay (LUD-06)](https://github.com/lnurl/luds/blob/luds/06.md) and is
backend-agnostic — [LNbits](https://lnbits.com) is the backend available today. Built on
the [Gacela](https://gacela-project.com) framework.

## Quick start

Requires PHP >= 8.3 and an LNbits wallet API key.

```bash
composer require php-lightning/lnaddress          # or clone this repo + composer install
cp lightning-config.dist.php lightning-config.php # settings
cp backends.dist.json backends.json               # per-user invoice backends
composer serve                                    # http://localhost:8080
```

```bash
curl 'http://localhost:8080/bob'              # → LNURL-pay params
curl 'http://localhost:8080/bob?amount=2000'  # → bolt11 invoice (2000 millisats)
```

```php
// lightning-config.php
use PhpLightning\Config\LightningConfig;

return (new LightningConfig())
    ->setDomain('yourdomain.com')
    ->setReceiver('default-receiver')
    ->setDescriptionTemplate('Pay to %s')                 // %s = the lightning address
    ->setSuccessMessage('Thanks for the payment!')
    ->setSendableRange(min: 100_000, max: 10_000_000_000) // millisats
    ->setCallbackUrl('https://yourdomain.com')
    ->addBackendsFile(getcwd() . '/backends.json');
```

```json
// backends.json
{
  "bob":   { "type": "lnbits", "api_key": "abc...123", "api_endpoint": "http://localhost:5000" },
  "alice": { "type": "lnbits", "api_key": "def...456", "api_endpoint": "http://localhost:5000" }
}
```

Wallets resolve `bob@yourdomain.com` through
`https://yourdomain.com/.well-known/lnurlp/bob`, so route that path to this app's
`/{username}` over HTTPS.

## Documentation

| Guide | Contents |
|---|---|
| [Getting started](docs/getting-started.md) | Install, configure, run, deploy |
| [Configuration](docs/configuration.md) | Every setter, defaults, backends file |
| [HTTP API](docs/api.md) | Routes, payloads, CORS, error objects |
| [Architecture](docs/architecture.md) | Modules, layers, adding a backend |
| [Development](docs/development.md) | Scripts, tests, static analysis, releasing |

Also: [CHANGELOG](CHANGELOG.md) ·
[wiki](https://github.com/php-lightning/lnaddress/wiki) ·
[demo template](https://github.com/php-lightning/demo-lnaddress)

## Development

```bash
composer test-all   # php-cs-fixer + psalm + phpstan + phpunit + rector (dry-run)
composer fix        # apply php-cs-fixer and rector changes
```

## Contributing

Issues and pull requests are welcome — read
[CONTRIBUTING](.github/CONTRIBUTING.md) and the
[Code of Conduct](.github/CODE_OF_CONDUCT.md), and run `composer test-all` first.

## License

MIT — see [LICENSE](LICENSE).
