# PHP Lightning Address

PHP Lightning Address is an easy way to get a [lightning address](https://lightningaddress.com/) in PHP.

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

## Usage / Development

Set up your custom config:

```bash
cp lightning-config.dist.php lightning-config.php
```

Run a local PHP server listening `public/index.php`

```bash
composer serve
```

### Demo template

We prepared a demo template, so you can use this project as a dependency. The benefits from this approach is that you can easily update your project with `composer update` whenever there are new features or improvements on this `lnaddress` repository.

> [https://github.com/php-lightning/demo-lnaddress](https://github.com/php-lightning/demo-lnaddress)

## Wiki

Check the wiki for more details: [https://github.com/php-lightning/lnaddress/wiki](https://github.com/php-lightning/lnaddress/wiki)

## Contributions

Feel free to open issues & PR if you want to contribute to this project.
