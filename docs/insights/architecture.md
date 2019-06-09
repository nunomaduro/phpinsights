# Architecture

The following insights are in organised in differents metrics :

* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes` <Badge text="Architecture\Classes" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Composer` <Badge text="Architecture\Composer" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Constants` <Badge text="Architecture\Constants" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Files` <Badge text="Architecture\Files" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Functions` <Badge text="Architecture\Functions" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Globally` <Badge text="Architecture\Globally" type="warn" vertical="middle"/>
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

## Class trait and interface length <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/>

This sniff checks the size of your classes/traits/interface

**Insight Class**: `ObjectCalisthenics\Sniffs\Files\ClassTraitAndInterfaceLengthSniff`

<details>
    <summary>Configuration</summary>

```php
    ObjectCalisthenics\Sniffs\Files\ClassTraitAndInterfaceLengthSniff::class => [
        'maxLength' => 200,
    ]
```
</details>

## Method per class limit <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/>

This sniff checks if the number of method per class is under a limit.

**Insight Class**: `ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff`

<details>
    <summary>Configuration</summary>

```php
    ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff::class => [
        'maxCount' => 10,
    ]
```
</details>


## Property per class limit <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/>

This sniff checks if the number of property per class is under a limit.

**Insight Class**: `ObjectCalisthenics\Sniffs\Metrics\PropertyPerClassLimitSniff`

<details>
    <summary>Configuration</summary>

```php
    ObjectCalisthenics\Sniffs\Metrics\PropertyPerClassLimitSniff::class => [
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

<!--
Insight template
##  <Badge text="^1.0"/> <Badge text="Architecture\Classes" type="warn"/>

This sniff

**Insight Class**: ``

<details>
    <summary>Configuration</summary>

```php

```
</details>
-->
