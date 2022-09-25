# PHP Lightning Address
PHP Lightning Address is an easy way to get a [lightning address](https://lightningaddress.com/) just with one php script file.

# Prerequisite
- [x] Web server with PHP backend
- [x] LNbits Lightning implementation backend
- [x] Web server **must** be able to be configured :
  - [x] to rewrite URL requests **OR**
  - [x] to let the PHP backend handle specific file(s) that don't have a `.php` extension

# Supported Lightning implementation
- [LNbits](https://github.com/lnbits/lnbits) 

More Lightning implementation will be supported in the future.

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

# Usage
## Rename & move the script to the right directory
Once the web server is configured to rewrite url requests `.well-known/lnurlp/anything` to `.well-known/lnurlp/anything.php`,  `lnaddress.php` script needs to be renamed with the wanted username part of the [lightning address](https://lightningaddress.com/), such as for a wanted ln address `ben@anything.dne`, the script must be named `ben.php`.

Then, the script needs to be in the subdirectory `.well-known/lnurlp/` of your web root directory of your web server such as https://www.yourwebsite.dne/.well-known/lnurlp/ points to your `$WEBROOT/.well-known/lnurlp/` directory.

## Set API Key
Please change the script line 15 `'api_key' => ''` in order to set your LNbits **invoice/read key**, it should then look like `'api_key' => 'deadbeefc0ffee1337abcdef01234567'`

## Customization
You might want to change some of the script variables, the following variable can be changed 
|     Variable    |                   comment                  |       default value      |
| :------------- | :----------------------------------------  | :----------------------- |
| `$description` | Description of the payment for the sender   | Pay to *user@domain.tld* |
| `$minSendable` | Minimum amount of **milisats** to send      | 100000 |
| `$maxSendable` | Maximum amount of **milisats** to send      | 10000000000 |
| `$image_file`  | Path to a JPG picture, it will be displayed on the sender confirmation screen | *no picture* |

**Please note that `$minSendable` and `$maxSendable` do not reflect the actual min/max sendable amount, it depends on the capacity of your LN backend**

## To add a new address
To add a new [lightning address](https://lightningaddress.com/), the only thing needed is copying and pasting the script, don't forget to change the API Key if you want the funds to be received on another wallet, and name the file with the user part of the [lightning address](https://lightningaddress.com/) wanted.

# Contributions
Feel free to open issues & PR if you want to contribute to this project.