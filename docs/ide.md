# IDE Integration

Since <Badge text="^1.10"/> version of PhpInsights, you can add links to files in PhpInsights
output. 

## Prerequisite & Troubleshouting

Links in terminal work thanks to [`symfony/console`](https://github.com/symfony/console)
from 4.3 version. Be sure you are using this version with `composer info symfony/console`.

If your terminal does not support hyperlinks, they will be rendered as normal 
and non-clickable text. 

That's why it's recommended to check out the 
[list of terminal emulators](https://gist.github.com/egmontkob/eb114294efbcd5adb1944c9f3cb5feda) 
that support hyperlinks.

## Configuration

In your `phpinsights.php` file, add the config `'ide' => 'myide'`.

For example:

```php
<?php

return [
    'ide' => 'vscode',
];
```

## Supported IDE 

You can fill `ide` config with the followings values:

* phpstorm
* sublime
* textmate
* macvim
* emacs
* atom 
* vscode

::: tip About PhpStorm
The phpstorm option is supported natively by PhpStorm on MacOS.

Windows requires [PhpStormProtocol](https://github.com/aik099/PhpStormProtocol) 
and Linux requires [phpstorm-url-handler](https://github.com/sanduhrs/phpstorm-url-handler).

You may also have to enable the [command-line launcher](https://www.jetbrains.com/help/phpstorm/working-with-the-ide-features-from-command-line.html).
:::

## Unsupported IDE

If you use another editor, the expected configuration value is a URL template 
that contains an `%f` placeholder where the file path is expected and `%l` 
placeholder for the line number.

For example:

```php
<?php

return [
    'ide' => 'myide://open?url=file://%f&line=%l',
];
```
