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
