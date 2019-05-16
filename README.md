<p align="center">

  <img alt="PHP Insights" src="https://raw.githubusercontent.com/nunomaduro/phpinsights/master/docs/banner.png" >

  <p align="center">
    <a href="https://travis-ci.org/nunomaduro/phpinsights"><img src="https://img.shields.io/travis/nunomaduro/phpinsights/master.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/v/stable.svg" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/license.svg" alt="License"></a>
  </p>
</p>


**PHP Insights** was created by, and is maintained by **[Nuno Maduro](https://github.com/nunomaduro)**, and is the perfect starting point to analyze the code quality of your PHP projects.
Carefully crafted to simplify the analysis of your code directly from your terminal.

## ✨ Features

- Analysis of **code quality** and **coding style**
- Beautiful overview of code **architecture** and it's **complexity**
- Designed to work out-of-the-box with **Laravel**, **Symfony**, and more
- Contains built-in checks for making code reliable, loosely coupled, **simple**, and **clean**
- Friendly console interface build on top of [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer), [PHPLOC](https://github.com/sebastianbergmann/phploc), and [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard)

## 👉🏻 Installation & Usage

> **Requires:**
- **[PHP 7.2+](https://php.net/releases/)**

First, install PHP Insights via the Composer package manager:
```bash
composer require nunomaduro/phpinsights:"^1.1.1" --dev
```

Then, use the `phpinsights` binary:
```bash
./vendor/bin/phpinsights
```

On Windows:
```cmd
.\vendor\bin\phpinsights.bat
```

### Within Laravel

First, you should publish the config-file with:
```bash
php artisan vendor:publish --provider="NunoMaduro\PhpInsights\Application\Adapters\Laravel\InsightsServiceProvider"
```

Then, use the `insights` Artisan command:
```bash
php artisan insights
```

### Within Symfony

First, you should create the config-file with:
```bash
cp vendor/nunomaduro/phpinsights/stubs/symfony.php phpinsights.php
```

Then, use the `phpinsights` binary:
```bash
./vendor/bin/phpinsights
```

On Windows:
```cmd
.\vendor\bin\phpinsights.bat
```

### No framework - customize `Insights`

You may customize `insights` creating and editing the configuration file:
```bash
cp vendor/nunomaduro/phpinsights/stubs/config.php phpinsights.php
```

### Continuous Integration

You can run PHP Insights in your CI by defining level you want to reach with the options `--min-quality`, `--min-complexity`, `--min-architecture`, `--min-style`.

If the minimum level defined is not reached, the console will return an exit error code.

```bash
./vendor/bin/phpinsights --no-interaction --min-quality=80 --min-complexity=90 --min-architecture=75 --min-style=95

# Within Laravel
php artisan insights --no-interaction --min-quality=80 --min-complexity=90 --min-architecture=75 --min-style=95
```

**Note**: The `--no-interaction` option is mandatory when it's launch in CI to avoid prompts.

All others are optional, so if you want to focus only on style, add the `--min-style` and forget others.

### Display issues omitted

PHP Insights console command have different verbosity levels, which determine the quantity of issues displayed. By default, commands display only the 3 first issues per `Insight`, but you can display them all with the `-v` option:
```bash
./vendor/bin/phpinsights -v
```

On Windows:
```cmd
.\vendor\bin\phpinsights.bat -v
```

### Allowed memory size of X bytes exhausted

If you encounter the error `Allowed memory size of XXXXX bytes exhausted`, the current workaround is to increase the memory limit:
```
php -d memory_limit=2000M ./vendor/bin/phpinsights
```

## 💡 How to contribute

The project is under development. As such, any help is welcome!

1. [Create a new insight from scratch](#create-a-new-insight)
2. [Add a new insight from PHP CS Sniff](#add-a-new-insight-from-php-cs-sniff)
3. [Create or improve create a preset for your favorite framework](#create-or-improve-create-a-preset-for-your-favorite-framework)
4. [Create the test suite](#create-the-test-suite)

### Create a new `Insight`

Imagine that you want to create a new `Insight` that doesn't allow the usage of final classes:

1. Create a new file under `src/Domain/Insights` with the content:

```php
final class ForbiddenFinalClasses extends Insight
{
    public function hasIssue(): bool
    {
        return (bool) count($this->collector->getConcreteFinalClasses());
    }

    public function getTitle(): string
    {
        return 'The use of `final` classes is prohibited';
    }
}
```

2. Attach the `Insight` to a specific inside `src/Domain/Metrics`:

```php
final class Classes implements HasInsights
{
    // ...

    public function getInsights(): array
    {
        return [
            ForbiddenFinalClasses::class,
        ];
    }
}
```

### Add a new insight from PHP CS Sniff

Are you aware of a PHPCS sniff that you would like to add to PHP Insights? You can add it in the following way:

1. Identify the related metric, and add it to the list of insights:

```php
final class Classes implements HasInsights
{
    // ...

    public function getInsights(): array
    {
        return [
            UnusedPropertySniff::class,
        ];
    }
}
```

### Create or improve create a preset for your favorite framework

Would you like to exclude a directory or remove an `Insight` for your favorite framework? You can add it in the following way:

> In this example we are going to use the Laravel Framework.

1. Open the file `src/Application/Adapters/Laravel/Preset.php` and update the config file:

```php
final class Preset implements PresetContract
{
    public static function getName(): string
    {
        return 'laravel';
    }

    public static function get(): array
    {
        return [
            'exclude' => [
                'config',
                'storage',
                'resources',
                'bootstrap',
                'nova',
                'database',
                'server.php',
                '_ide_helper.php',
                '_ide_helper_models.php',
                'public',
            ],
            'add' => [
                Classes::class => [
                    ForbiddenFinalClasses::class,
                ],
            ],

            'remove' => [
                AlphabeticallySortedUsesSniff::class,
                DeclareStrictTypesSniff::class,
                DisallowMixedTypeHintSniff::class,
                ForbiddenDefineFunctions::class,
                ForbiddenNormalClasses::class,
                ForbiddenTraits::class,
                TypeHintDeclarationSniff::class,
            ],

            'config' => [
                ForbiddenPrivateMethods::class => [
                    'title' => 'The usage of private methods is not idiomatic in Laravel.',
                ],
                ForbiddenDefineGlobalConstants::class => [
                    'ignore' => ['LARAVEL_START'],
                ],
                ForbiddenFunctionsSniff::class => [
                    'forbiddenFunctions' => [
                        'dd' => null,
                        'dump' => null,
                    ],
                ],
            ],
        ];
    }
}
```

### Create the test suite

At the moment, this package doesn't have any test. Would you like to contribute? This is the perfect task.

# 👏 Contributors

Thank you to all the people who have already contributed to PHP Insights!

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore -->
<table><tr><td align="center"><a href="https://nunomaduro.com"><img src="https://avatars2.githubusercontent.com/u/5457236?v=4" width="100px;" alt="Nuno Maduro"/><br /><sub><b>Nuno Maduro</b></sub></a><br /></td><td align="center"><a href="https://github.com/caneco"><img src="https://avatars0.githubusercontent.com/u/502041?v=4" width="100px;" alt="Caneco"/><br /><sub><b>Caneco</b></sub></a><br /></td><td align="center"><a href="https://seriquynh.com"><img src="https://avatars3.githubusercontent.com/u/16967350?v=4" width="100px;" alt="Quynh Xuan Nguyen"/><br /><sub><b>Quynh Xuan Nguyen</b></sub></a><br /></td><td align="center"><a href="https://github.com/mikeerickson"><img src="https://avatars1.githubusercontent.com/u/183153?v=4" width="100px;" alt="Mike Erickson"/><br /><sub><b>Mike Erickson</b></sub></a><br /></td><td align="center"><a href="https://github.com/szepeviktor/debian-server-tools/blob/master/CV.md"><img src="https://avatars3.githubusercontent.com/u/952007?v=4" width="100px;" alt="Viktor Szépe"/><br /><sub><b>Viktor Szépe</b></sub></a><br /></td><td align="center"><a href="https://pxgamer.xyz"><img src="https://avatars0.githubusercontent.com/u/1899334?v=4" width="100px;" alt="Owen Voke"/><br /><sub><b>Owen Voke</b></sub></a><br /></td></tr></table>

<!-- ALL-CONTRIBUTORS-LIST:END -->

## 🆓 License
PHP Insights is open-sourced software licensed under the [MIT license](LICENSE.md).
