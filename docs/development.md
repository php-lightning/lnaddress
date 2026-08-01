# Development

```bash
composer install
composer test-all
```

## Composer scripts

| Script | What it runs |
|---|---|
| `composer test-all` | `quality` + PHPUnit + `rector --dry-run` — the CI gate |
| `composer quality` | php-cs-fixer (dry-run), Psalm, PHPStan |
| `composer phpunit` | PHPUnit only |
| `composer test-coverage` | PHPUnit with HTML coverage in `data/coverage-html` |
| `composer fix` | php-cs-fixer + Rector, writing changes |
| `composer ctal` | clear static caches, `fix`, then `test-all` |
| `composer static-clear-cache` | Clear the Psalm and PHPStan caches |
| `composer serve` | `php -S localhost:8080 public/index.php` |

Static analysis runs with `XDEBUG_MODE=off`; coverage flips it to `coverage`.

## Tests

- `tests/Unit/` mirrors `src/` and needs no bootstrap — plain PHPUnit with stubs/mocks.
- `tests/Feature/` boots Gacela end to end. `tests/Feature/backends.json` supplies the
  fixture users and `tests/Feature/Fake/FakeHttpApi` replaces the HTTP client, so no
  network call ever happens.

A feature test bootstraps Gacela with in-line config, then overrides the module's provider
to inject the fake:

```php
Gacela::bootstrap(__DIR__, static function (GacelaConfig $config): void {
    $config->resetInMemoryCache();
    $config->addAppConfigKeyValues(
        (new LightningConfig())
            ->setCallbackUrl('https://callback.url/receiver')
            ->setDomain('domain.com')
            ->setReceiver('receiver')
            ->setSendableRange(1_000, 10_000)
            ->addBackendsFile(__DIR__ . DIRECTORY_SEPARATOR . 'backends.json')
            ->jsonSerialize(),
    );
});

Gacela::overrideExistingResolvedClass(
    InvoiceDependencyProvider::class,
    new class() extends AbstractProvider {
        public function provideModuleDependencies(Container $container): void
        {
            $container->set(InvoiceDependencyProvider::HTTP_API, static fn () => new FakeHttpApi());
        }
    },
);
```

PHPUnit 12 is in use: attributes instead of annotations, `createStub()` / `createMock()`,
no `withConsecutive()`.

## Static analysis

- **Psalm** at `errorLevel="1"`, target PHP 8.3, PHPUnit plugin enabled.
- **PHPStan** at `level: max` with the gacela extension (`modulesNamespace: PhpLightning`).
- **Rector** with the PHP 8.3 set plus dead-code and code-quality sets, over `src/` and
  `tests/`.
- **php-cs-fixer** with `.php-cs-fixer.dist.php`.

Fix findings at the source instead of widening the ignore lists. If PHPStan dies with an
out-of-memory error inside `resultCache.php`, the cause is a stale result cache — run
`vendor/bin/phpstan clear-result-cache`, not a bigger memory limit.

## Conventions

- `declare(strict_types=1);` everywhere; classes `final`, `readonly` when stateless.
- Typed class constants (`public const string FOO = '…';`).
- Domain code depends on interfaces; wiring lives in the Factory / DependencyProvider.
- DTOs live in `src/Shared/Transfer/` with the `Transfer` suffix.
- [Conventional commits](https://www.conventionalcommits.org/); `ref:` for refactors.

## CI

`.github/workflows/ci.yml` runs on every push and pull request:

1. **Coding Guidelines** — php-cs-fixer dry-run
2. **Type Checker** — Psalm (with Shepherd) and PHPStan
3. **Tests** — PHPUnit on PHP 8.3, 8.4 and 8.5, with locked and highest dependencies

Coverage is not collected in CI; run `composer test-coverage` locally for an HTML report.

## Releasing

1. Move the `Unreleased` entries in [`CHANGELOG.md`](../CHANGELOG.md) under the new
   version and date, and refresh the compare links.
2. Tag `main` (`0.10.0` — tags are unprefixed since 0.2.0).
3. Push the tag and publish the GitHub release with that changelog section as the notes.
