# lnaddress

PHP library serving a [Lightning Address](https://lightningaddress.com/) endpoint
(LNURL-pay / LUD-06). Built on [Gacela](https://gacela-project.com) (modular monolith) +
`gacela-project/router`. PHP >= 8.3, PSR-4 `PhpLightning\` -> `src/`,
`PhpLightningTest\` -> `tests/`.

Full docs live in [`docs/`](docs/) — architecture, config reference, HTTP API.

## Request flow

`public/index.php` bootstraps Gacela (`gacela.php`) -> `Router` -> `InvoiceRoutesPlugin`
(route + `CorsMiddleware` + `InvoiceExceptionHandler`) -> `InvoiceController` ->
`InvoiceFacade`.

- `GET /{username}` without `amount` -> `getCallbackUrl()` -> LNURL-pay params
- `GET /{username}?amount=<millisats>` -> `generateInvoice()` -> bolt11 under `pr`
- Errors bubble to `InvoiceExceptionHandler`, which returns `{status: ERROR, reason: …}` —
  controllers do not try/catch.

Config comes from `lightning-config.dist.php` (overridable by gitignored
`lightning-config.php`) plus users/backends in `backends.json`.

## Module layout (Gacela)

Modules live in `src/<Module>/`. A module's public API is its **Facade** — never reach
into another module's Application/Domain/Infrastructure.

```
src/Invoice/
  InvoiceFacade.php              entry point; @extends AbstractFacade<InvoiceFactory>
  InvoiceFactory.php             wiring;      @extends AbstractFactory<InvoiceConfig>
  InvoiceConfig.php              typed config reads
  InvoiceDependencyProvider.php  external deps (HTTP_API)
  Application/                   use cases: CallbackUrl, InvoiceGenerator
  Domain/                        pure logic + interfaces (BackendInvoice, CallbackUrl, Http)
  Infrastructure/                Controller, Handler, Middleware, Http, Plugin
src/Config/                      LightningConfig builder, BackendsConfig, BackendType enum
src/Shared/                      ConfigKey, Transfer DTOs, Value objects
```

Gacela generics matter: `AbstractFacade`/`AbstractFactory`/`AbstractProvider` are
templated, so annotate with `@extends ...<T>`. For docblock service resolution use
`ServiceResolverAwareTrait` (`DocBlockResolverAwareTrait` is deprecated).

Config keys are constants in `PhpLightning\Shared\Config\ConfigKey`, shared by the writer
(`LightningConfig`) and reader (`InvoiceConfig`) — add new keys there, not as literals.

## Conventions

- `declare(strict_types=1);` everywhere; classes `final`, `readonly` when stateless.
- Typed class constants (`public const string FOO = '…';`) — enforced by the Rector php83 set.
- Domain depends on interfaces; concrete wiring lives in the Factory / DependencyProvider.
- DTO suffixes: `Transfer` for data flowing into operations, `Result` for handler output.
  No `T` prefix in this repo. See `src/Shared/Transfer/`.
- Conventional commits, `ref:` instead of `refactor:`.

## Testing

- `tests/Unit/` mirrors `src/` — plain PHPUnit, no bootstrap.
- `tests/Feature/` boots Gacela and overrides `InvoiceDependencyProvider` to inject
  `FakeHttpApi`; fixtures in `tests/Feature/backends.json`.
- PHPUnit 12: attributes, `createStub()`/`createMock()`, no `withConsecutive`.

## Commands

```bash
composer test-all   # quality (cs, psalm, phpstan) + phpunit + rector --dry-run
composer quality    # csrun + psalm + phpstan
composer phpunit    # tests only
composer fix        # php-cs-fixer + rector (writes changes)
composer ctal       # clear static caches, fix, then full suite
composer serve      # php -S localhost:8080 public/index.php
```

Psalm runs at errorLevel 1, PHPStan at level max. If PHPStan dies with an OOM inside
`resultCache.php`, that is a stale cache — run `vendor/bin/phpstan clear-result-cache`,
not a bigger memory limit.
