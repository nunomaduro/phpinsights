# Configure Insights

The default configuration attached with `PHP Insights` is opiniated and may be not convenient for you.

Here you will learn how to configure PHPInsights for your project. 

Before continuing, please create a `phpinsights.php` file by reading the [configuration docs](/configuration.md).

You should have the following structure:

```php
<?php

declare(strict_types=1);

return [
    'preset' => 'default',
    'exclude' => [
        //  'path/to/directory-or-file'
    ],
    'add' => [
        //  ExampleMetric::class => [
        //      ExampleInsight::class,
        //  ]
    ],
    'remove' => [
        //  ExampleInsight::class,
    ],
    'config' => [
        //  ExampleInsight::class => [
        //      'key' => 'value',
        //  ],
    ],
];
```

## Exclude folder or files

By default, `phpinsights` will analyse all your php files in your project directory, except folders `bower_components`, `node_modules` and `vendor`.

::: tip For others preset
In addition to theses folders :
- With the **laravel** preset, `phpinsights` will exclude `config`, `storage`, `resources`, `bootstrap`, `nova`, `database`, `server.php`, `_ide_helper.php`, `_ide_helper_models.php`, `app/Providers/TelescopeServiceProvider.php` and `public`.
- With the **symfony** preset, `phpinsights` will exclude `var`, `translations`, `config`, and `public`.
- With the **magento2** preset, `phpinsights` will exclude `bin`, `dev`, `generated`, `lib`, `phpserver`, `pub`, `setup`, `update`, `var`, `app/autoload.php`, `app/bootstrap.php`, `app/functions.php` and `index.php`.
- With the **drupal** preset, `phpinsights` will exclude `core`, `modules/contrib`, `sites`, `profiles/contrib`, and `themes/contrib`.
:::

In your `phpinsights.php` file, you can add to the `exclude` key everything you want to exclude.

For example:

```php
    'exclude' => [
        'src/Migrations', // will exclude Migrations Folder in src
        '*Repository.php', // will exclude every php files that match pattern
        'src/Kernel.php' // will exclude this file only
    ],
```

## Add Insights

If you create an Insight, or an Insight is not enabled, you can enable it in the `add` section.

For example, if you want to enable "Fully Qualified ClassName In Annotation":

```php
    'add' => [
        \NunoMaduro\PhpInsights\Domain\Metrics\Code\Comment::class => [
            \SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedClassNameInAnnotationSniff::class
        ]
    ]
```
::: tip
You could also simplify the namespace with `use My\Insight\Namespace;`
:::

::: tip
Although `PHPInsights` has it's own insights, it can handle Sniffs from [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) and Fixers from [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer/).
So you can add every sniff or fixers that implements `PHP_CodeSniffer\Sniffs\Sniff` or `PhpCsFixer\Fixer\FixerInterface`.
:::

## Remove Insights

If there is an insight that goes against your standards, you can add it in the `remove` section.

For example, if you don't like adding a space after not (`! $myVariable`):
```php
    'remove' => [
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff::class,
    ]
```

::: tip
To know the className of an Insight, launch `phpinsights` with `-v` option (verbose)
:::

## Configure Insights

The `config` section allows you to refine default insight configuration.

For example, to increase the line length limits:
```php
    'config' => [
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 160
        ]
    ]
```

You can also configure the `exclude` parameter on each insight, to disallow an
insight on a specific file.

For example, to remove "Unused Parameters" Insight only for some file:
```php
    'config' => [
        \SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class => [
            'exclude' => [
                'src/Path/To/My/File.php',
                'src/Path/To/Other/File.php',
            ],
        ],
    ],
```

<Badge text="^2.0"/> For insights that come from PHP-CS-Fixer and implements `WhitespacesAwareFixerInterface`, you can also configure the indentation to respect: 

```php
    'configure' => [
        \PhpCsFixer\Fixer\Basic\BraceFixer::class => [
            'indent' => '  ',
		],
    ],
```
