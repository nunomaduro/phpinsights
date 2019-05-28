# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [v1.4.0]
### Added
- Drupal preset ([#120](https://github.com/nunomaduro/phpinsights/pull/120))
- Display insight class name on verbose mode ([#139](https://github.com/nunomaduro/phpinsights/pull/139))

## [v1.3.1]
### Fixed
- Missing option `--disable-security-check` ([#106](https://github.com/nunomaduro/phpinsights/pull/106))
- Overwrite of existing preset config options ([#111](https://github.com/nunomaduro/phpinsights/pull/111))
- [Laravel Preset] Ignores `TelescopeServiceProvider::class` ([#113](https://github.com/nunomaduro/phpinsights/pull/113))

## [v1.3.0]
### Added
- Magento2 preset ([#102](https://github.com/nunomaduro/phpinsights/pull/102))

### Fixed
- Exit error code on security issues ([#106](https://github.com/nunomaduro/phpinsights/pull/106))

## [v1.2.1]
### Fixed
- Wrong insight `RequireShortTernaryOperatorSniff` ([7e10c18](https://github.com/nunomaduro/phpinsights/commit/7e10c186ed0923423e4650151644f12daa9875ed))
- Missing dependency `ext-iconv` ([#90](https://github.com/nunomaduro/phpinsights/pull/90))

## [v1.2.0]
### Adds
- Forces getting `1.2` using `composer require`

## [v1.1.1]
### Fixed
- Lock `symplify` dependencies ([167292c](https://github.com/nunomaduro/phpinsights/commit/167292c172da52c48f3e434884893cdeeeec4db6))

## [v1.1.0]
### Adds
- Options `--min-quality`, `--min-complexity`, `--min-architecture`, `--min-style` ([#67](https://github.com/nunomaduro/phpinsights/pull/67))

### Fixed
- Issue when composer `require` key is empty ([#76](https://github.com/nunomaduro/phpinsights/pull/76))
- Issues displaying scores above 10 ([#57](https://github.com/nunomaduro/phpinsights/pull/57))

## [v1.0.5]
### Fixed
- [Laravel Preset] Ignores `ide_helper_models` ([#51](https://github.com/nunomaduro/phpinsights/pull/51))

## [v1.0.4]
### Fixed
- PHP warning while using `--no-interaction` option ([f68e13a](https://github.com/nunomaduro/phpinsights/commit/f68e13a26770aa1984415ed848947177ff9939cd))

## [v1.0.3]
### Fixed
- Usage on composer global ([ccecff5](https://github.com/nunomaduro/phpinsights/commit/ccecff580949184b6e1bf9bba33c2c173c480c4b))

## [v1.0.2]
### Fixed
- Usage on windows ([8bae26f](https://github.com/nunomaduro/phpinsights/commit/8bae26f096f6f9e39e3dc2e6c03ec4acb4e3f802))

## [v1.0.1]
### Fixed
- [Laravel Preset] Ignores `_ide_helper.php` ([#46](https://github.com/nunomaduro/phpinsights/pull/46))

## [v1.0.0]
### Added
- First version
