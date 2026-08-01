---
description: Scaffold a new Gacela module following this repo's layout
allowed-tools: Bash, Read, Write, Edit, Grep, Glob
argument-hint: <ModuleName> [what it does]
---

Create module `$1` in `src/$1/`, mirroring `src/Invoice/`:

- `$1Facade.php` — `/** @extends AbstractFacade<$1Factory> */`, thin delegation only.
- `$1Factory.php` — `/** @extends AbstractFactory<$1Config> */`, builds Application/Domain objects.
- `$1Config.php` — extends `AbstractConfig`, typed getters over `$this->get(...)`.
- `$1DependencyProvider.php` — only if external deps are needed;
  `/** @extends AbstractProvider<$1Config> */`, constants as `public const string`.
- `Application/`, `Domain/`, `Infrastructure/` as needed; Domain talks to interfaces only.

Rules: `declare(strict_types=1);`, `final` classes, `readonly` where stateless, DTOs in
`src/Shared/Transfer/` with the `Transfer` suffix. Cross-module access goes through the
other module's Facade.

Add unit tests under `tests/Unit/$1/` mirroring the source paths; add a Feature test only
if the module needs Gacela bootstrapping. Finish with `composer test-all`.

Context: $ARGUMENTS
