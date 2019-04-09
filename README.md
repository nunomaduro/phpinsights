<p align="center">

  <img alt="PHP Insights" src="https://raw.githubusercontent.com/nunomaduro/phpinsights/feat/first/docs/banner.png" >

  <p align="center">
    <a href="https://travis-ci.org/nunomaduro/phpinsights"><img src="https://img.shields.io/travis/nunomaduro/phpinsights/master.svg" alt="Build Status"></img></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/v/stable.svg" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/license.svg" alt="License"></a>
  </p>
</p>


**PHP Insights** created and maintained by [Nuno Maduro](https://github.com/nunomaduro), is the perfect starting point to analyze the code quality of your PHP projects.
Carefully crafted to simplify the analysis of your code directly on your terminal.

**🚨 This project is under development. Don't use it!**.


## ✨ Features

- Analysis of **lines of code** and cyclomatic complexity
- Beautiful overview of your **code structure** and it's dependencies
- Contains built-in checks for making code reliable, loosely coupled, **simple**, and **clean**

## 👉🏻 Installation & Usage

> **Requires:**
- **[PHP 7.2+](https://php.net/releases/)**

First, install PHP Insights via the Composer package manager:

```bash
composer require nunomaduro/phpinsights:dev-feat/first
```

### Without frameworks

Use the `phpinsights` binary:

```bash
php ./vendor/bin/phpinsights
```

### Within Laravel

You can publish the config-file with:

```bash
php artisan vendor:publish --provider="NunoMaduro\PhpInsights\Application\Adapters\Laravel\InsightsServiceProvider"
```

Open `config/insights.php`, and update the preset to `laravel`.

Use the `insights` Artisan command:

```bash
php artisan insights
```

## 💡 How to contribute

The project is under development. As such, any help is welcome!

### Create a new `Insight`

Imagine that you want to create a new `Insight` that don't allow the usage of final classes:

1. Create a new file under `Domain\Insights` with the content:

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

2. Attach the `Insight` to a specific inside `Domain/Metrics/`:

```php
final class ClassesFinal implements HasValue, HasPercentage, HasInsights
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

## 🆓 License
PHP Insights is open-sourced software licensed under the [MIT license](LICENSE.md).
