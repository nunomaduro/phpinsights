# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [v2.0.0]
### Added
- Minimal PHP version is now PHP 7.4 ([#391](https://github.com/nunomaduro/phpinsights/pull/391))
- Support multiple path to analyse ([#384](https://github.com/nunomaduro/phpinsights/pull/384))
- Autofixer ([#337](https://github.com/nunomaduro/phpinsights/pull/337))
- Syntax check Insight is now using [PHP-Parallel-lint](https://github.com/php-parallel-lint/PHP-Parallel-Lint) ([#373](https://github.com/nunomaduro/phpinsights/pull/373), [#437](https://github.com/nunomaduro/phpinsights/pull/437), [#487](https://github.com/nunomaduro/phpinsights/pull/487)) 
- Cache on analysis ([#407](https://github.com/nunomaduro/phpinsights/pull/407))
- Allow to override preset config ([#420](https://github.com/nunomaduro/phpinsights/pull/420))
- Support Composer v2 ([#433](https://github.com/nunomaduro/phpinsights/pull/433), [#442](https://github.com/nunomaduro/phpinsights/pull/442))
- Support phploc v7 ([#428](https://github.com/nunomaduro/phpinsights/pull/428))
- Parallelization of analysis ([#414](https://github.com/nunomaduro/phpinsights/pull/414))
- Support PHP8 ([#448](https://github.com/nunomaduro/phpinsights/pull/448))
- CheckStyle report use the dot notation format to be using with Checkstyle parsers ([#464](https://github.com/nunomaduro/phpinsights/pull/464)) 
- Allow to use `version` in composer.json ([#473](https://github.com/nunomaduro/phpinsights/pull/473)) 
- Upgrade to [Slevomat/Coding-standard v7](https://github.com/slevomat/coding-standard) ([#477](https://github.com/nunomaduro/phpinsights/pull/477))
- New option to show summary only ([#481](https://github.com/nunomaduro/phpinsights/pull/481))
- Support PHP-CS-Fixer v3 ([#490](https://github.com/nunomaduro/phpinsights/pull/490))
- CodeClimate Formatter ([#498](https://github.com/nunomaduro/phpinsights/pull/498))
- Configuring Fixers indentation ([#502](https://github.com/nunomaduro/phpinsights/pull/502))
- Diff output configurable ([#482](https://github.com/nunomaduro/phpinsights/pull/482))

### Fixed
- Performance issues ([#382](https://github.com/nunomaduro/phpinsights/pull/382), [#387](https://github.com/nunomaduro/phpinsights/pull/387), [#405](https://github.com/nunomaduro/phpinsights/pull/405))
- Check that lock file exist ([#389](https://github.com/nunomaduro/phpinsights/pull/389))
- Avoid conflict with phpcs config file ([#402](https://github.com/nunomaduro/phpinsights/pull/402))
- Clean display message without file target ([#404](https://github.com/nunomaduro/phpinsights/pull/404))
- IDE Link with colors ([#413](https://github.com/nunomaduro/phpinsights/pull/413))
- No error trigger when Details Message issue contains console styling ([#447](https://github.com/nunomaduro/phpinsights/pull/447))
- Excluded files from Cyclomatic Complexity are no more take in account for score ([#460](https://github.com/nunomaduro/phpinsights/pull/460))

### Changed
- Refactoring code ([#391](https://github.com/nunomaduro/phpinsights/pull/391), [#393](https://github.com/nunomaduro/phpinsights/pull/393))
- Process Security Checks during the project inspection ([#406](https://github.com/nunomaduro/phpinsights/pull/406)) 
- Drop Travis-CI and use GithubAction ([#448](https://github.com/nunomaduro/phpinsights/pull/448))
- Drop `object-calisthenics/phpcs-calisthenics-rules` dependency ([#477](https://github.com/nunomaduro/phpinsights/pull/477))

### Deprecated
- Some Insights hare now deprecated or namespace changed. Find them [here (#477)][#477](https://github.com/nunomaduro/phpinsights/pull/477)

## [v1.14.1]
### Changed
- Switch service to discover vulnerabilities ([#453](https://github.com/nunomaduro/phpinsights/pull/453))


## [v1.14.0]
### Added
- `ddd` & `tinker` as forbidden method names Laravel ([#364](https://github.com/nunomaduro/phpinsights/pull/364))
- Support to Laravel 7 ([#369](https://github.com/nunomaduro/phpinsights/pull/369))
- `min-requirements` configurable through file ([#362](https://github.com/nunomaduro/phpinsights/pull/362))

### Fixed
- Hanging on invalid php code ([#358](https://github.com/nunomaduro/phpinsights/pull/358))
- Aggregates diffs in the same error ([#377](https://github.com/nunomaduro/phpinsights/pull/377))

### Changed
- Removed Static closure advice on Laravel ([e9d2a62](https://github.com/nunomaduro/phpinsights/commit/e9d2a62bb97468f2886622f54be10de1bc3eeb24))

## [v1.13.0]
### Added
- Sort issues by file, line, function and lastly message ([#348](https://github.com/nunomaduro/phpinsights/pull/348))
- Support multiple formatters ([#357](https://github.com/nunomaduro/phpinsights/pull/357))
- Support custom class formatters ([#357](https://github.com/nunomaduro/phpinsights/pull/357))
- Added diff from fixer to JSON formatter ([#356](https://github.com/nunomaduro/phpinsights/pull/356))
- Check PHP version from composer to determine native type hint ([#347](https://github.com/nunomaduro/phpinsights/pull/347))
- Added GitHub Action formatter ([#344](https://github.com/nunomaduro/phpinsights/pull/344))

### Fixed
- Prevent failing on empty namespace ([#352](https://github.com/nunomaduro/phpinsights/pull/352))

## [v1.12.0]
### Added
- Text coloring per section ([#339](https://github.com/nunomaduro/phpinsights/pull/339))
- Support to slevomat coding standard 6 ([#342](https://github.com/nunomaduro/phpinsights/pull/342))

## [v1.11.1]
### Fixed
- Exclusion in ForbiddenFinalClasses Insight ([#326](https://github.com/nunomaduro/phpinsights/pull/326))

## [v1.11.0]
### Added
- Support to Symfony 5 ([#324](https://github.com/nunomaduro/phpinsights/pull/324))

## [v1.10.3]
### Fixed
- Assert IDE is defined before resolving it ([#319](https://github.com/nunomaduro/phpinsights/pull/319))

## [v1.10.2]
### Fixed
- Exclusion in ForbiddenTraits Insight ([#316](https://github.com/nunomaduro/phpinsights/pull/316))

## [v1.10.1]
### Changed
- Reorganize place of some Insights ([#313](https://github.com/nunomaduro/phpinsights/pull/313))

### Fixed
- Duplicate empty comment Insight ([#311](https://github.com/nunomaduro/phpinsights/pull/311))
- Remove VoidReturnFixer in laravel Preset ([#312](https://github.com/nunomaduro/phpinsights/pull/312))

## [v1.10.0]
### Added
- Checkstyle formatter ([#271](https://github.com/nunomaduro/phpinsights/pull/271))
- PHP CS Fixer Wrapper ([#219](https://github.com/nunomaduro/phpinsights/pull/219))
- Responsive view in Console ([#273](https://github.com/nunomaduro/phpinsights/pull/273))
- IDE Url handler ([#265](https://github.com/nunomaduro/phpinsights/pull/265))
- Offline usage ([#292](https://github.com/nunomaduro/phpinsights/pull/292))
- Directory exclusion in Insights config ([#293](https://github.com/nunomaduro/phpinsights/pull/293))
- Favicon in website ([#281](https://github.com/nunomaduro/phpinsights/pull/281))

### Changed
- Drop easy coding standard dependency ([#252](https://github.com/nunomaduro/phpinsights/pull/252))
- Use a configuration class ([#283](https://github.com/nunomaduro/phpinsights/pull/283))
- Improve verbose progress bar ([#291](https://github.com/nunomaduro/phpinsights/pull/291))

### Fixed
- Silence Warnings ([#253](https://github.com/nunomaduro/phpinsights/pull/253))

## [v1.9.0]
### Added
- Better support to Laravel Lumen ([#247](https://github.com/nunomaduro/phpinsights/pull/247))

### Fixed
- Issue when internal insight code is an integer ([6c6650f](https://github.com/nunomaduro/phpinsights/commit/6c6650fec1101222e2a63e2736aaf07cd0b152be))

## [v1.8.1]
### Fixed
- Throwable sniffs ([#249](https://github.com/nunomaduro/phpinsights/pull/249))

## [v1.8.0]
### Added
- Authorize analyse of one file or specific directory ([#195](https://github.com/nunomaduro/phpinsights/pull/195))
- Configure search use in annotation ([#196](https://github.com/nunomaduro/phpinsights/pull/196))
- Add changelog to website ([#204](https://github.com/nunomaduro/phpinsights/pull/204))
- Json & Console formatters ([#201](https://github.com/nunomaduro/phpinsights/pull/201))

### Fixed
- Merge config with default config ([#196](https://github.com/nunomaduro/phpinsights/pull/196))

## [v1.7.0]
### Added
- ComposerMustBeValid and ComposerLockMustBeFresh insights ([#169](https://github.com/nunomaduro/phpinsights/pull/169))
- CyclomaticComplexityIsHigh max complexity is now configurable ([#190](https://github.com/nunomaduro/phpinsights/pull/190))
- Possibility of ignoring files on specific insight ([#182](https://github.com/nunomaduro/phpinsights/pull/182))
- Possibility of disable ForbiddenSecurityInsight ([#175](https://github.com/nunomaduro/phpinsights/pull/175)) ([#187](https://github.com/nunomaduro/phpinsights/pull/187))

### Fixed
- Non used files are no included anymore while requiring php insights ([#189](https://github.com/nunomaduro/phpinsights/pull/189))

## [v1.6.0]
### Added
- Laravel preset now ignores model attribute setters ([#154](https://github.com/nunomaduro/phpinsights/pull/154))
- Ignores package managers folders by default ([#144](https://github.com/nunomaduro/phpinsights/pull/144))
- Ignores `blade.php` files ([#155](https://github.com/nunomaduro/phpinsights/pull/155))
- You can now exclude files and directories ([#75](https://github.com/nunomaduro/phpinsights/pull/75))
- Automatic docker image build on new release ([#160](https://github.com/nunomaduro/phpinsights/pull/160))

### Changed
- Upgraded `sensiolabs/security-checker` dependency to `^6.0` ([#158](https://github.com/nunomaduro/phpinsights/pull/158))

### Fixed
- Fixed a bug with optional type hints increasing cyclomatic complexity ([#150](https://github.com/nunomaduro/phpinsights/pull/150))

## [v1.5.0]
### Added
- Better support with nette dependencies

### Fixed
- Complexity over `100.0`
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
