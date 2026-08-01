# Configuration

Two files drive the app, both read at bootstrap through `gacela.php`:

| File | Purpose | In git |
|---|---|---|
| `lightning-config.dist.php` | Shipped defaults | yes |
| `lightning-config.php` | Your overrides — wins over the dist file | no |
| `backends.dist.json` | Example backends | yes |
| `backends.json` | Your users + API keys | no |

## `LightningConfig`

`lightning-config.php` returns a `PhpLightning\Config\LightningConfig`. Only the values you
set are serialized, so anything you leave out keeps its default.

```php
use PhpLightning\Config\LightningConfig;

return (new LightningConfig())
    ->setDomain('example.com')
    ->setReceiver('bob')
    ->setDescriptionTemplate('Pay to %s')
    ->setSuccessMessage('Payment received!')
    ->setInvoiceMemo('coffee fund')
    ->setSendableRange(min: 100_000, max: 10_000_000_000)
    ->setCallbackUrl('https://example.com')
    ->addBackendsFile(getcwd() . DIRECTORY_SEPARATOR . 'backends.json');
```

| Setter | Config key | Default | Notes |
|---|---|---|---|
| `setDomain(string)` | `domain` | `$_SERVER['HTTP_HOST']`, else `localhost` | A full URL is accepted; only the host is kept |
| `setReceiver(string)` | `receiver` | `unknown-receiver` | Username used when the URL has none |
| `setCallbackUrl(string)` | `callback-url` | `undefined:callback-url` | Returned as `callback`; wallets must be able to reach it |
| `setSendableRange(int $min, int $max)` | `sendable-range` | `100_000` – `10_000_000_000` msat | Amounts outside the range are rejected |
| `setDescriptionTemplate(string)` | `description-template` | `Pay to %s` | `%s` is the Lightning Address; shown in the payer's wallet |
| `setSuccessMessage(string)` | `success-message` | `Payment received!` | Returned as `successAction.message` |
| `setInvoiceMemo(string)` | `invoice-memo` | `''` | Memo stored on the invoice at the backend |
| `addBackendsFile(string $path)` | `backends` | — | Throws if the file does not exist; callable more than once |
| `addBackend(string $username, BackendConfigInterface)` | `backends` | — | Register a backend without a JSON file |

Amounts are always **millisatoshis** (msat); the default range is 100 sat to
10 000 000 sat. The key strings live in `PhpLightning\Shared\Config\ConfigKey`, shared by
the writer (`LightningConfig`) and the reader (`InvoiceConfig`) so they cannot drift.

## Backends file (`backends.json`)

A map of username to backend settings. The username is the local part of the Lightning
Address (`bob@example.com` -> `bob`) and the path segment of the request (`/bob`).

```json
{
  "bob": {
    "type": "lnbits",
    "api_key": "abc...123",
    "api_endpoint": "https://legend.lnbits.com"
  },
  "alice": {
    "type": "lnbits",
    "api_key": "def...456",
    "api_endpoint": "http://localhost:5000"
  }
}
```

- `type` is resolved through the `BackendType` enum; an unknown value fails fast with
  `Unknown backend type "x". Supported types: lnbits`.
- `api_endpoint` loses any trailing slash; the client calls
  `{api_endpoint}/api/v1/payments`.
- `api_key` is an LNbits **invoice/read** key — it only needs invoice-creation rights.
- Requesting a username that is not listed fails with
  `Missing backend options for <username>`.

Treat `backends.json` as a secret; it is gitignored on purpose.

### Without a JSON file

```php
use PhpLightning\Config\Backend\LnBitsBackendConfig;

$config->addBackend('bob', LnBitsBackendConfig::withEndpointAndKey('http://localhost:5000', 'abc...123'));
```

## Gacela bootstrap (`gacela.php`)

```php
return static function (GacelaConfig $config): void {
    $config
        ->enableFileCache()
        ->addAppConfig('lightning-config.dist.php', 'lightning-config.php')
        ->extendGacelaConfig(RouterGacelaConfig::class)
        ->addPlugin(InvoiceRoutesPlugin::class);
};
```

- `addAppConfig(dist, override)` is what makes `lightning-config.php` optional.
- `enableFileCache()` caches resolved classes/config under `.gacela/`; delete that
  directory if a stale value survives a config change.
- `InvoiceRoutesPlugin` registers the route, the CORS middleware and the exception
  handler.
