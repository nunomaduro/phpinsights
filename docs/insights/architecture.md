# Architecture

The following insights are in organised in differents metrics :

* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes` <Badge text="Architecture\Classes" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Composer` <Badge text="Architecture\Composer" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Constants` <Badge text="Architecture\Constants" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Files` <Badge text="Architecture\Files" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Functions` <Badge text="Architecture\Functions" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Interfaces` <Badge text="Architecture\Interfaces" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Namespaces` <Badge text="Architecture\Namespaces" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Traits` <Badge text="Architecture\Traits" type="warn" vertical="middle"/>

## Forbidden normal classes <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/>

This insight disallow usage of normal class. Class must be `abstract` or `final`.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses`

## Valid class name <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/>

This sniff ensures classes are in camel caps, and the first letter is capitalised.

**Insight Class**: `PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff`

## Class declaration <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/>

This sniff checks if the declaration of the class is correct

**Insight Class**: `PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff`

## Class trait and interface length <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/> <Badge text="configurable"/>

This sniff checks the size of your classes/traits/interface

**Insight Class**: `ObjectCalisthenics\Sniffs\Files\ClassTraitAndInterfaceLengthSniff`

<details>
    <summary>Configuration</summary>

```php
\ObjectCalisthenics\Sniffs\Files\ClassTraitAndInterfaceLengthSniff::class => [
    'maxLength' => 200,
]
```
</details>

## Method per class limit <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/> <Badge text="configurable"/>

This sniff checks if the number of method per class is under a limit.

**Insight Class**: `ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff`

<details>
    <summary>Configuration</summary>

```php
\ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff::class => [
    'maxCount' => 10,
]
```
</details>


## Property per class limit <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/> <Badge text="configurable"/>

This sniff checks if the number of property per class is under a limit.

**Insight Class**: `ObjectCalisthenics\Sniffs\Metrics\PropertyPerClassLimitSniff`

<details>
    <summary>Configuration</summary>

```php
\ObjectCalisthenics\Sniffs\Metrics\PropertyPerClassLimitSniff::class => [
    'maxCount' => 10,
]
```
</details>

## One class per file <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/>

This sniff checks that only one class is declared per file.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Files\OneClassPerFileSniff`

## Superfluous interface naming <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/>

This sniff reports use of superfluous prefix or suffix "Interface" for interfaces.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff`

## Superfluous abstract class naming <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/>

This sniff reports use of superfluous prefix or suffix "Abstract" for abstract class.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\SuperfluousAbstractClassNamingSniff`

## `composer.json` must exist <Badge text="^1.0"/> <Badge text="Architecture\Composer" type="warn"/>

This insight verifies there is `composer.json`.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustExist`

## The name property in the `composer.json` <Badge text="^1.0"/> <Badge text="Architecture\Composer" type="warn"/>

This insight checks if the name section in `composer.json` don't contains default values (e.g. `laravel/laravel` or `symfony/symfony`).

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustContainName`

## Composer.json must be valid <Badge text="^1.7"/> <Badge text="Architecture\Composer" type="warn"/>

This insight checks if the `composer.json` is valid.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustBeValid`

## Composer.lock must be fresh <Badge text="^1.7"/> <Badge text="Architecture\Composer" type="warn"/>

This insight verifies that the `composer.lock` is not outdated.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerLockMustBeFresh`

## Define `globals` is prohibited <Badge text="^1.0"/> <Badge text="Architecture\Constants" type="warn"/>

This insight disallow defining `globals`.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants`

##  <Badge text="^1.0"/> <Badge text="Architecture\Files" type="warn"/>

This sniff reports use of superfluous prefix or suffix "Exception" for exceptions.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff`

## Function length <Badge text="^1.0"/> <Badge text="Architecture\Functions" type="warn"/> <Badge text="configurable"/>

This sniff checks size of functions

**Insight Class**: `ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff`

<details>
    <summary>Configuration</summary>

```php
\ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff::class => [
    'maxLength' => 20,
]
```
</details>

## One interface per file <Badge text="^1.0"/> <Badge text="Architecture\Interfaces" type="warn"/>

This sniff checks that only one interface is declared per file.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Files\OneInterfacePerFileSniff`

## Namespace declaration <Badge text="^1.0"/> <Badge text="Architecture\Namespaces" type="warn"/>

This sniff enforces one space after namespace, disallows content between namespace name and semicolon and disallows use of bracketed syntax.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\NamespaceDeclarationSniff`

## Useless Alias <Badge text="^1.0"/> <Badge text="Architecture\Namespaces" type="warn"/>

This sniff looks for use alias that is same as unqualified name.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\UselessAliasSniff`

## Compound namespace depth <Badge text="^1.0"/> <Badge text="Architecture\Namespaces" type="warn"/>

This sniff verifies that compound namespaces are not defined too deep.

**Insight Class**: `PHP_CodeSniffer\Standards\PSR12\Sniffs\Namespaces\CompoundNamespaceDepthSniff`

## Forbidden traits <Badge text="^1.0"/> <Badge text="Architecture\Traits" type="warn"/>

This insight disallow traits usage.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits`

## One trait per file <Badge text="^1.0"/> <Badge text="Architecture\Traits" type="warn"/>

This sniff checks that only one trait is declared per file

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Files\OneTraitPerFileSniff`

## Superfluous trait naming <Badge text="^1.0"/> <Badge text="Architecture\Traits" type="warn"/>

This sniff reports use of superfluous prefix or suffix "Trait" for traits.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\SuperfluousTraitNamingSniff`

<!--
Insight template
##  <Badge text="^1.0"/> <Badge text="Architecture\Traits" type="warn"/>

This sniff

**Insight Class**: ``

<details>
    <summary>Configuration</summary>

```php

```
</details>
-->
