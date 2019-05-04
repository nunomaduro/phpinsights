<p align="center">

  <img alt="PHP Insights" src="https://raw.githubusercontent.com/nunomaduro/phpinsights/feat/first/docs/banner.png" >

  <p align="center">
    <a href="https://travis-ci.org/nunomaduro/phpinsights"><img src="https://img.shields.io/travis/nunomaduro/phpinsights/master.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/v/stable.svg" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/license.svg" alt="License"></a>
  </p>
</p>


**PHP Insights** created and maintained by **[Nuno Maduro](https://github.com/nunomaduro)**, is the perfect starting point to analyze the code quality of your PHP projects.
Carefully crafted to simplify the analysis of your code directly from your terminal.

## ✨ Features

- Analysis of **code quality** and **coding style**
- Beautiful overview of code **architecture** and it's **complexity**
- Contains built-in checks for making code reliable, loosely coupled, **simple**, and **clean**
- Friendly console interface build on top of [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer), [PHPLOC](https://github.com/sebastianbergmann/phploc), and [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard)

## 👉🏻 Installation & Usage

> **Requires:**
- **[PHP 7.2+](https://php.net/releases/)**

First, install PHP Insights via the Composer package manager:
```bash
composer require nunomaduro/phpinsights --dev
```

Then, use the `phpinsights` binary:
```bash
php ./vendor/bin/phpinsights
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
cp vendor/nunomaduro/phpinsights/stubs/config.php phpinsights.php
```

Then, use the `phpinsights` binary:
```bash
php ./vendor/bin/phpinsights
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
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (bool) count($this->collector->getConcreteFinalClasses());
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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
/**
 * @internal
 */
final class Preset implements PresetContract
{
    /**
     * {@inheritDoc}
     */
    public static function getName(): string
    {
        return 'laravel';
    }

    /**
     * {@inheritDoc}
     */
    public static function get(): array
    {
        return [
            'exclude' => [
                'storage',
                'resources',
                'bootstrap',
                'database',
                'server.php',
            ],
            'add' => [
                // ...
            ],
            'remove' => [
                // ...
            ],
            'config' => [
                ForbiddenDefineGlobalConstants::class => [
                    'ignore' => ['LARAVEL_START'],
                ],
            ],
        ];
    }
}
```

### Create the test suite

At the moment, this package doesn't have any test. Would you like to contribute? This is the perfect task.

## 🆓 License
PHP Insights is open-sourced software licensed under the [MIT license](LICENSE.md).
