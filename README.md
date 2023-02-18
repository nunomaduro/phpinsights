<p align="center">
  <img src="/art/logo.gif" width="350" alt="PHP Insights">
  <img src="/art/preview.png" width="882" alt="PHP Insights Preview">
  <p align="center">
    <a href="https://github.com/nunomaduro/phpinsights/actions/workflows/test.yaml"><img src="https://github.com/nunomaduro/phpinsights/actions/workflows/test.yaml/badge.svg" alt="Unit Tests"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/v/stable.svg" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/nunomaduro/phpinsights"><img src="https://poser.pugx.org/nunomaduro/phpinsights/license.svg" alt="License"></a>
  </p>
  <p align="center">
    <strong>For full documentation, visit <a href="https://phpinsights.com">phpinsights.com</a></strong>.
  </p>
</p>

**PHP Insights** was carefully crafted to simplify the analysis of your code directly from your terminal, and is the perfect starting point to analyze the code quality of your PHP projects.
It was created by **[Nuno Maduro](https://github.com/nunomaduro)**, logo design **[Caneco](https://github.com/caneco)**, and currently is maintained by **[Chris Gmyr](https://github.com/cmgmyr)**, **[Jibé Barth](https://github.com/Jibbarth)**, and **[Steve McDougall](https://github.com/JustSteveKing)**.

## 🚀 Quick start


### First, install:

```
composer require nunomaduro/phpinsights --dev
```

### Then, use it:
```
./vendor/bin/phpinsights
```

### For Laravel:
First, publish the configuration file:
```
php artisan vendor:publish --provider="NunoMaduro\PhpInsights\Application\Adapters\Laravel\InsightsServiceProvider"
```

Then, use it:
```
php artisan insights
```

## ✨ Features

- Analysis of **code quality** and **coding style**
- Beautiful overview of code **architecture** and its **complexity**
- Designed to work out-of-the-box with **Laravel**, **Symfony**, **Yii**, **Magento**, and more
- Contains built-in checks for making code reliable, loosely coupled, **simple**, and **clean**

## 💖 Support the development
**Do you like this project? Support it by donating**

Click the "💖 Sponsor" at the top of this repo.

PHP Insights is open-sourced software licensed under the [MIT license](LICENSE.md).
