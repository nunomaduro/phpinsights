# Get started

> **Requires:** [PHP 7.2+](https://php.net/releases/)

First, install PHP Insights via the `Composer` package manager:
```bash
composer require nunomaduro/phpinsights --dev
```

Then, use the `phpinsights` binary:
```bash
# Mac & Linux
./vendor/bin/phpinsights

# Windows
.\vendor\bin\phpinsights.bat
```

## Within Laravel

First, you should publish the config-file with:
```bash
php artisan vendor:publish --provider="NunoMaduro\PhpInsights\Application\Adapters\Laravel\InsightsServiceProvider"
```

Then, use the `insights` Artisan command:
```bash
php artisan insights
```

> Note for Laravel 7 users: phpinsights requires [PHP 7.3+](https://php.net/releases/), and `PHPUnit 9.0+`. For upgrading PHPUnit, you can use following command:
 ```bash
 composer require --dev phpunit/phpunit:^9.0 --update-with-dependencies
 ```

## Within Lumen

Because we cannot use Artisan's publish command within a Lumen project you must manually copy the config file into your project:

```bash
cp vendor/nunomaduro/phpinsights/stubs/laravel.php config/insights.php
```

Then register the `phpinsights` provider and load the configuration into the application within your `bootstrap/app.php` file:

```php
$app->register(\NunoMaduro\PhpInsights\Application\Adapters\Laravel\InsightsServiceProvider::class);
$app->configure('insights');
```

And setup is done, so you can now run `phpinsights` with the following command:

```bash
php artisan insights
```

## With Docker

You can also use `phpinsights` via Docker:
```bash
docker run -it --rm -v $(pwd):/app nunomaduro/phpinsights
```

## Analyse a sub-directory or a specific file

You can ask `phpinsights` to analyse only a directory or even a specific file by providing path with `analyse` command:

```bash
# For a directory
./vendor/bin/phpinsights analyse path/to/analyse

# For a file
./vendor/bin/phpinsights analyse path/to/analyse.php
```

In laravel, launch command as usual with your path:

```bash
php artisan insights path/to/analyse
```

## Fixing errors automatically <Badge text="^2.0"/>

Some Insights support automatic fixing. 
To fix your code automatically, two way are possibles: 

* Add `--fix` option to your command. The output will be the classical output, with a summary of all issues fixed.
* Or launch `phpinsights fix [directory]`

```bash
# Classical command with --fix option
vendor/bin/phpinsights analyse path/to/analyse --fix

# In laravel
php artisan insights path/to/analyse --fix

# Just fix
vendor/bin/phpinsights fix path/to/analyse
```

## Analyse multiple paths <Badge text="^2.0"/>
You can ask `phpinsights` to analyse multiple directories, multiple files or even combining them by providing multiple paths with `analyse` command:

```bash
# For multiple directories
./vendor/bin/phpinsights analyse path/to/dir1 path/to/dir2

# For multiple files
./vendor/bin/phpinsights analyse path/to/file1.php path/to/file2.php

# Combined
./vendor/bin/phpinsights analyse path/to/dir path/to/file.php
```

In laravel, launch command as usual with your paths:

```bash
php artisan insights path/to/dir path/to/file.php
```

## Specify composer file <Badge text="^2.0"/>

Optionally, you can specify your composer file by adding the `composer` flag as below:

```bash
./vendor/bin/phpinsights analyse --composer=/var/www/composer.json
```

## Formatting the output

For changing the output format you can add the `format` flag. The following formats are supported:

- console
- json
- checkstyle

```bash
./vendor/bin/phpinsights analyse --format=json
```

## Saving output to file

You can pipe the result to a file or to anywhere you like.
A common use case is parsing the output formatted as json to a json file.

```bash
./vendor/bin/phpinsights analyse --format=json > test.json
```

When piping the result remember to add the no interaction flag `-n`, as the part where you need to interact is also getting piped. (the json format does not have any interaction)
While piping data, if you want the progress bar to refresh itself instead of printing a new one, add the `--ansi` flag.

## Allowed memory size of X bytes exhausted

If you encounter the error `Allowed memory size of XXXXX bytes exhausted`, the current workaround is to increase the memory limit:
```
php -d memory_limit=2000M ./vendor/bin/phpinsights
```

## Display issues omitted

The PHP Insights console command has different verbosity levels, which determine the quantity of issues displayed. By default, commands display only the 3 first issues per `Insight`, but you can display them all with the `-v` option:
```bash
./vendor/bin/phpinsights -v
```

## Avoid Composer conflicts

If you have trouble while requiring `phpinsights` with composer, try install it with [bamarni/composer-bin-plugin](https://github.com/bamarni/composer-bin-plugin) to isolate it from others dependencies:

```bash
composer require --dev bamarni/composer-bin-plugin
composer bin phpinsights require nunomaduro/phpinsights
./vendor/bin/phpinsights
```

## Flush cache results <Badge text="^2.0"/>

Between 2 analysis, issues are cached. 
PHPInsights is smart enough to invalidate cache when it detect changes in your code, but you may completly flush cache before analysis by adding `--flush-cache` flag.
