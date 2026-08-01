# HTTP API

A single route serves the whole [LNURL-pay flow](https://github.com/lnurl/luds/blob/luds/06.md):
`GET|OPTIONS /{username?}`. The `amount` query parameter decides which step you get.

| Request | Step | Response |
|---|---|---|
| `GET /{username}` | LNURL-pay request | pay params (`tag: payRequest`) |
| `GET /{username}?amount={msat}` | LNURL-pay callback | bolt11 invoice under `pr` |
| `OPTIONS /{username}` | CORS preflight | empty body, CORS headers |

`username` is optional — without it the configured `receiver` is used. `amount` is in
**millisatoshis**; the backend is billed in sats (`amount / 1000`).

Every response carries `Access-Control-Allow-Origin: *` (`CorsMiddleware`), so
browser-based wallets can call the endpoint directly.

## 1. Pay params

```bash
curl 'https://example.com/bob'
```

```json
{
  "callback": "https://example.com",
  "maxSendable": 10000000000,
  "minSendable": 100000,
  "metadata": "[[\"text/plain\",\"Pay to bob@example.com\"],[\"text/identifier\",\"bob@example.com\"]]",
  "tag": "payRequest",
  "commentAllowed": false
}
```

- `callback` is `setCallbackUrl()` verbatim — wallets append `?amount=`.
- `metadata` is built by `LnurlPayMetadata` with `json_encode`, so quotes in your
  description or address cannot break the JSON.
- `commentAllowed` is always `false` (LUD-12 comments are not implemented).

## 2. Invoice

```bash
curl 'https://example.com/bob?amount=2000'
```

```json
{
  "pr": "lnbc20n1p...",
  "status": "OK",
  "memo": "",
  "successAction": { "tag": "message", "message": "Payment received!" },
  "routes": [],
  "disposable": false,
  "error": null
}
```

The bolt11 invoice is under `pr`, as LUD-06 requires. The backend receives the amount in
sats plus `description_hash` (sha256 of the metadata) and `unhashed_description`.

## Errors

Errors come back as the LNURL error object. Uncaught exceptions are converted by
`InvoiceExceptionHandler`, registered globally in `InvoiceRoutesPlugin`, so no controller
needs a try/catch.

```json
{ "status": "ERROR", "reason": "Amount is not between minimum and maximum sendable amount" }
```

```json
{ "status": "ERROR", "reason": "Missing backend options for carol" }
```

An unreachable backend answers with `status: ERROR` and
`error: Backend "LnBits" unreachable`.

## Serving a real Lightning Address

Wallets resolve `bob@example.com` through:

```
https://example.com/.well-known/lnurlp/bob
```

Route that path to `/{username}`. With nginx:

```nginx
location /.well-known/lnurlp/ {
    rewrite ^/\.well-known/lnurlp/(.*)$ /$1 last;
}

location / {
    try_files $uri /index.php$is_args$args;
}
```

Requirements for interoperability: HTTPS, JSON responses, and a `callback` URL reachable
from the public internet.

## Calling it without HTTP

The facade is usable directly, which is what the feature tests do:

```php
use Gacela\Framework\Gacela;
use PhpLightning\Invoice\InvoiceFacade;

Gacela::bootstrap(__DIR__);

$facade = new InvoiceFacade();
$payParams = $facade->getCallbackUrl('bob');
$invoice = $facade->generateInvoice('bob', 2_000); // millisats
```
