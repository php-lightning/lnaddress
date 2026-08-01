# Documentation

The landing page for this folder is published at
<https://php-lightning.github.io/lnaddress/> (`index.html`, deployed by
`.github/workflows/pages.yml`).

`php-lightning/lnaddress` turns a domain you control into a
[Lightning Address](https://lightningaddress.com/) provider: it implements
[LNURL-pay (LUD-06)](https://github.com/lnurl/luds/blob/luds/06.md) and issues invoices
through a pluggable backend ([LNbits](https://lnbits.com/) today).

| Guide | What's inside |
|---|---|
| [Getting started](getting-started.md) | Install, configure, run locally, deploy |
| [Configuration](configuration.md) | Every `LightningConfig` setter, `backends.json`, defaults |
| [HTTP API](api.md) | Routes, payloads, CORS, error objects |
| [Architecture](architecture.md) | Gacela modules, layers, request flow, adding a backend |
| [Development](development.md) | Scripts, tests, static analysis, releasing |

## In one minute

```bash
composer install                                   # copies backends.dist.json → backends.json
cp lightning-config.dist.php lightning-config.php  # domain, receiver, callback URL
composer serve                                     # http://localhost:8080
```

```bash
curl 'http://localhost:8080/bob'              # LNURL-pay params
curl 'http://localhost:8080/bob?amount=2000'  # bolt11 invoice for 2000 msat
```

## Requirements

- PHP >= 8.3
- An LNbits instance (or compatible endpoint) with an invoice/read API key
- HTTPS on a public domain for real-world use
