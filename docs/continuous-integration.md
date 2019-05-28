# Continuous Integration

You can run PHP Insights in your CI by defining level you want to reach with the options `--min-quality`, `--min-complexity`, `--min-architecture`, `--min-style`.

If the minimum level defined is not reached, the console will return an exit error code.

```bash
./vendor/bin/phpinsights --no-interaction --min-quality=80 --min-complexity=90 --min-architecture=75 --min-style=95

# Within Laravel
php artisan insights --no-interaction --min-quality=80 --min-complexity=90 --min-architecture=75 --min-style=95
```

**Note**: The `--no-interaction` option is mandatory when it's launch in CI to avoid prompts.

All others are optional, so if you want to focus only on style, add the `--min-style` and forget others.

## Disable Security Check

In case you develop a library or a plugin, it could be compatible with a large panel of dependencies versions.
So you can launch your `composer update` with `--prefer-lower` flag to tests theses minimum version.

As `phpinsights` return an exit error code if security issues are found, you can disable this check by adding the `--disable-security-check` option :

```bash
./vendor/bin/phpinsights --no-interaction --disable-security-check
```

**Note** : For a project inspection, you **should** never use this option to keep your project safe.
