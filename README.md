<p align="center">

  <img alt="PHP Insights" src="https://raw.githubusercontent.com/nunomaduro/phpinsights/feat/first/docs/banner.png" >

  <p align="center">
    <a href="https://travis-ci.org/nunomaduro/phpinsights"><img src="https://img.shields.io/travis/nunomaduro/phpinsights/master.svg" alt="Build Status"></img></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/v/stable.svg" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/license.svg" alt="License"></a>
  </p>
</p>


**PHP Insights** was created by, and is maintained by [Nuno Maduro](https://github.com/nunomaduro), and is the perfect starting point to analyse the code quality of your project. It is
carefully crafted to simplify the analyse of your code directly on your terminal.

**🚨 This project is under development. Don't use it!**.


## ✨ Features

- Analyse of **lines of code** and cyclomatic complexity
- Beautiful overview of your **code structure** and it's dependencies
- Contains built-in checks for making code reliable, loosely couple, **simple**, and **clean**

## 👉🏻 Installation & Usage

> **Requires:**
- **[PHP 7.2+](https://php.net/releases/)**

First, install PHP Insights via the Composer package manager:

```bash
composer require nunomaduro/phpinsights:dev-feat/first
```

Then use the `phpinsights` binary:

```bash
php ./vendor/bin/phpinsights
```

## 💡 How to contribute

The project is under development. As such, any help is welcome!

### Create a new `Insight`

1. Create a new file under `Domain\Insights` with the content:

```php
final class FooUsage extends Insight
{
    /**
     * Checks if there is an issue.
     */
    public function hasIssue(): bool
    {
        return true;
    }

    /**
     * Describes the problem.
     */
    public function getTitle(): string
    {
        return 'The use of `foo` is prohibited';
    }
}
```

2. Attach the `Insight` to a specific inside `Domain/Metrics/`:

```php
final class Bar implements Metric, HasInsights
{
    /**
     * Returns the insights classes applied on the metric.
     *
     * @return string[]
     */
    public function getInsights(): array
    {
        return [
            FooUsage::class,
        ];
    }
}
```

## 🆓 License
PHP Insights is an open-sourced software licensed under the [MIT license](LICENSE.md).
