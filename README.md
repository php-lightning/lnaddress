# PHP Lightning Address
PHP Lightning Address is an easy way to get a [lightning address](https://lightningaddress.com/) in PHP.

<p align="center">
  <a href="https://github.com/php-lightning/lightning-address/actions">
    <img src="https://github.com/php-lightning/lightning-address/workflows/CI/badge.svg" alt="GitHub Build Status">
  </a>
  <a href="https://scrutinizer-ci.com/g/php-lightning/lightning-address/?branch=main">
    <img src="https://scrutinizer-ci.com/g/php-lightning/lightning-address/badges/quality-score.png?b=main" alt="Scrutinizer Code Quality">
  </a>
  <a href="https://scrutinizer-ci.com/g/php-lightning/lightning-address/?branch=main">
    <img src="https://scrutinizer-ci.com/g/php-lightning/lightning-address/badges/coverage.png?b=main" alt="Scrutinizer Code Coverage">
  </a>
  <a href="https://shepherd.dev/github/php-lightning/lightning-address">
    <img src="https://shepherd.dev/github/php-lightning/lightning-address/coverage.svg" alt="Psalm Type-coverage Status">
  </a>
  <a href="https://github.com/php-lightning/lightning-address/blob/master/LICENSE">
    <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="MIT Software License">
  </a>
</p>

### Installation

```bash
composer require php-lightning/lightning-address
```

Then you can use it directly from your vendor like:

#### get a callback-url 
```bash
vendor/bin/lnaddress callback-url

OUTPUT example:
{
    "callback": "https:\/\/your-domain.com\/custom-receiver",
    "maxSendable": 10000000000,
    "minSendable": 100000,
    "metadata": "[[\"text\/plain\",\"Pay to custom-receiver@your-domain.com\"],[\"text\/identifier\",\"custom-receiver@your-domain.com\"]]",
    "tag": "payRequest",
    "commentAllowed": false
}

```

#### request an invoice
```bash
php index.php 10000000

OUTPUT example:
{
    "pr": "No payment_request found",
    "status": "OK",
    "successAction": {
        "tag": "message",
        "message": "Payment received!"
    },
    "routes": [],
    "disposable": false
}

```

----

## Table of contents
- [Prerequisite](#prerequisite)
- [Supported Lightning implementation](#supported-lightning-implementation)
- [Prerequisite web server configuration](#prerequisite-web-server-configuration)
  * [Configure web server to rewrite URL](#configure-web-server-to-rewrite-url)
    + [Nginx](#nginx)
    + [Apache](#apache)
  * [Without rewriting URL](#without-rewriting-url)
- [Usage](#usage)
  * [Rename & move the script to the right directory](#rename--move-the-script-to-the-right-directory)
  * [Set API endpoint & API Key](#set-api-endpoint--api-key)
  * [Customization](#customization)
  * [To add a new address](#to-add-a-new-address)
- [Contributions](#contributions)

## Prerequisite
- [x] Web server with PHP backend
- [x] LNbits Lightning implementation backend
- Please note that **LNbits doesn't need to be on the same server**
- *You can use https://legend.lnbits.com/ for testing purpose*
- [x] Web server **must** be able to be configured
  - [x] to rewrite URL requests 
  - _**OR**_
  - [x] to let the PHP backend handle specific file(s) that don't have a `.php` extension

## Supported Lightning implementation
- [LNbits](https://github.com/lnbits/lnbits) 

More Lightning implementation will be supported in the future.

## Lightning Address flowchart
<img src="images/lnaddr_workflow.png"  width="30%" height="30%">

# Prerequisite web server configuration
## Configure web server to rewrite URL
### Nginx
Nginx configuration
```
location /.well-known/lnurlp {
    rewrite ^/.well-known/lnurlp/(.*)$ /.well-known/lnurlp/$1.php last;
}
```

### Apache
Apache configuration

You need to put a `.htaccess` file in the `.well-known/lnurlp/` of your web root directory with the following content
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME}.php -f
RewriteRule (.*) $1.php [L]
```
Please note that `.htaccess` file will not be read if the Apache configuration doesn't allow configuration overriding, please see Apache documentation regarding `AllowOverride` directive.

## Without rewriting URL
You might be able to configure your web server to let the PHP backend handle specific files, in that way the script can be named without `.php` extension and URL rewriting is thus not needed.

## Usage

### Rename & move the script to the right directory
Once the web server is configured to rewrite url requests `.well-known/lnurlp/anything` to `.well-known/lnurlp/anything.php`,  `lnaddress.php` script needs to be renamed with the wanted username part of the [lightning address](https://lightningaddress.com/), such as for a wanted ln address `ben@anything.dne`, the script must be named `ben.php`.

Then, the script needs to be in the subdirectory `.well-known/lnurlp/` of your web root directory of your web server such as https://www.yourwebsite.dne/.well-known/lnurlp/ points to your `$WEBROOT/.well-known/lnurlp/` directory.

### Set API endpoint & API Key

The following lines (14 & 15) need to be changed according to your `api_endpoint` and `api_key` that your LNbits wallet provides, be sure to use the **invoice/read key**.

```php
$backend_options['lnbits'] = [
        'api_endpoint' => 'http://localhost:5000',  // lnbits endpoint : protocol://host:port
        'api_key' => ''                             // put your lnbits read key here
];
```

### Customization

The following variable can be changed 
|     Variable    |                   comment                         |       default value      |
| :------------- | :------------------------------------------------  | :----------------------- |
| `$description` | Description of the payment for the sender          | Pay to *user@domain.tld* |
| `$success_msg` | Confirmation message to display on payment success | Payment received!        |
| `$minSendable` | Minimum amount of **millisats** to send            | 100000                   |
| `$maxSendable` | Maximum amount of **millisats** to send            | 10000000000              |
| `$image_file`  | Path to a JPG picture, displayed on the sender confirmation screen | *no picture* |

> **Please note that `$minSendable` and `$maxSendable` do not reflect the actual min/max sendable amount, it depends on the capacity of your LN backend**

## To add a new address

To add a new [lightning address](https://lightningaddress.com/), the only thing needed is copying and pasting the script, **don't forget to change the API Key if you want the funds to be received on another wallet**, and name the file with the user part of the [lightning address](https://lightningaddress.com/) wanted.

## Contributions

Feel free to open issues & PR if you want to contribute to this project.
