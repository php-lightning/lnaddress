# Changelog

All notable changes to this project are documented in this file.

## [Unreleased]

**Full Changelog**: https://github.com/php-lightning/lnaddress/compare/0.9.0...main

## [0.9.0] - 2026-07-23

- feat: DX overhaul and upgrade to gacela 1.19 / router 0.13
- fix: use LUD-06 `pr` key for the invoice callback response
- feat: add CORS middleware and global exception handler (router 0.13)
- chore: modernize dev tooling (PHPUnit 10, PHPStan 2, Rector 2)
- fix: enable coverage driver (`XDEBUG_MODE=coverage`) for Scrutinizer build
- docs: add CHANGELOG.md with release history

**Full Changelog**: https://github.com/php-lightning/lnaddress/compare/0.8.0...0.9.0

## [0.8.0] - 2025-05-25

- Support PHP >=8.2
- Add invoice description and memo customization
- Fix LnBits implementation

**Full Changelog**: https://github.com/php-lightning/lnaddress/compare/0.7.0...0.8.0

## [0.7.0] - 2023-06-20

- Allow add backends as json (#21)
- Move "gacela config" to `gacela.php` file
- Use `getcwd()` when `addBackendsFile` on `config.dist.php`

**Full Changelog**: https://github.com/php-lightning/lnaddress/compare/0.6.0...0.7.0

## [0.6.0] - 2023-04-17

- Update [Gacela Router 0.4](https://github.com/gacela-project/router/releases/tag/0.4.0)

**Full Changelog**: https://github.com/php-lightning/lnaddress/compare/0.5.0...0.6.0

## [0.5.0] - 2023-04-10

- Implement HTTP controllers for the endpoints using the new [gacela-project/router](https://github.com/gacela-project/router) library (inspired by [chico-framework](https://github.com/Tito-Kati/chico-framework))
- Among other improvements and refactorings

Thanks to @Bashy, @JesusValera and @Chemaclass.

**Full Changelog**: https://github.com/php-lightning/lnaddress/compare/0.4.0...0.5.0

## [0.4.0] - 2023-04-06

- Fix domain default param in config (#8) — @Bashy
- Rename project from `lightning-address` to `lnaddress` (#9)
- Create VO `SendableRange` (#11) — @JesusValera
- Fix POST http request (#10) — @Bashy
- Get callback-url in `index.php` when no GET amount (#12)
- Rename `lightning-config.php` to `lightning-config.dist.php` (#13)
- Remove `bin/lnaddress` script command (#14)
- Make callback-url configurable (#15) — @Bashy

**New Contributors**: @JesusValera made their first contribution in #11.

**Full Changelog**: https://github.com/php-lightning/lnaddress/compare/0.3.0...0.4.0

## [0.3.0] - 2023-04-03

- Add readme badges (#4)
- Create callback url command (#3)
- Add fluent interface for config (#5)
- Refactor api module (#6)
- Update readme installation (#7)

**Full Changelog**: https://github.com/php-lightning/lightning-address/compare/0.2.0...0.3.0

## [0.2.0] - 2023-03-31

- Modernize the PHP script (#2)
- Add the library to Packagist, installable as a vendor dependency

**Full Changelog**: https://github.com/php-lightning/lightning-address/compare/v0.1.0...0.2.0

## [v0.1.0] - 2022-10-30

- Improve `README.md`
- Add `$success_msg` as a customizable variable

[Unreleased]: https://github.com/php-lightning/lnaddress/compare/0.9.0...main
[0.9.0]: https://github.com/php-lightning/lnaddress/releases/tag/0.9.0
[0.8.0]: https://github.com/php-lightning/lnaddress/releases/tag/0.8.0
[0.7.0]: https://github.com/php-lightning/lnaddress/releases/tag/0.7.0
[0.6.0]: https://github.com/php-lightning/lnaddress/releases/tag/0.6.0
[0.5.0]: https://github.com/php-lightning/lnaddress/releases/tag/0.5.0
[0.4.0]: https://github.com/php-lightning/lnaddress/releases/tag/0.4.0
[0.3.0]: https://github.com/php-lightning/lnaddress/releases/tag/0.3.0
[0.2.0]: https://github.com/php-lightning/lnaddress/releases/tag/0.2.0
[v0.1.0]: https://github.com/php-lightning/lnaddress/releases/tag/v0.1.0
