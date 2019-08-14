# Contribute

The project is under development. As such, any help is welcome!

[[TOC]]

## Create a new `Insight`

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

## Add a new insight from PHP CS Sniff

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

## Create or improve create a preset for your favorite framework

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

## Create a new `Formatter`

The package has support for formatting the result. 
All formats implements the contract `src/Application/Console/Contracts/Formatter`.

You are welcome to contribute with new formats or improve on the ones we already have.
