# Continuous Integration

You can run PHP Insights in your CI by defining a level you want to reach with the options `--min-quality`, `--min-complexity`, `--min-architecture`, `--min-style`.

If the minimum level defined is not reached, the console will return an exit error code.

```bash
./vendor/bin/phpinsights --no-interaction --min-quality=80 --min-complexity=90 --min-architecture=75 --min-style=95

# Within Laravel
php artisan insights --no-interaction --min-quality=80 --min-complexity=90 --min-architecture=75 --min-style=95
```

These options can also be found in the configuration file, so no arguments needs to be passed when calling the command.  
Command arguments has higher priority than configuration values.

**Note**: The `--no-interaction` option is mandatory when it's launched in CI to avoid prompts.

All others are optional, so if you want to focus only on style, add the `--min-style` and forget others.

## Disable Security Check

In case you develop a library or a plugin, it could be compatible with a large panel of dependencies versions.
So you can launch your `composer update` with `--prefer-lower` flag to tests theses minimum version.

As `phpinsights` returns an exit error code if security issues are found, you can disable this check by adding the `--disable-security-check` option :

```bash
./vendor/bin/phpinsights --no-interaction --disable-security-check
```

**Note** : For a project inspection, you **should** never use this option to keep your project safe.

## Github Action <Badge text="^1.13"/>

If you use Github Action, you can launch PHP Insights with `--format=github-action` option.
With that, annotations with issues will be added in Pull request.


```yaml
#.github/workflows/pr.yml
name: CI
on:
    - pull_request

jobs:
    phpinsights:
        runs-on: ubuntu-latest
        name: PHP Insights checks
        steps:
            - uses: actions/checkout@v2
            - uses: shivammathur/setup-php@v1
              with:
                  php-version: 7.3
            - run: composer install --prefer-dist --no-progress --no-suggest
            - run: vendor/bin/phpinsights -n --ansi --format=github-action
```

![example with github action](./github-action.png)
