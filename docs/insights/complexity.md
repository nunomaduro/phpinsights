# Complexity

For now the Complexity section is only one Metric consisting of multiple insights:

* `NunoMaduro\PhpInsights\Domain\Metrics\Complexity\Complexity` <Badge text="Complexity" type="warn" vertical="middle"/>

## Class Cyclomatic Complexity is high <Badge text="^1.0"/> <Badge text="Complexity" type="warn"/>

This insight checks total method cyclomatic complexity of each class, the lower the score the easier your code is to understand. It raises an issue if complexity is over `5`.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh`

<details>
    <summary>Configuration</summary>

```php
\NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class => [
     'maxComplexity' => 5,
]
```
</details>

## Average Class Method Cyclomatic Complexity is high <Badge text="^2.12"/> <Badge text="Complexity" type="warn"/>

This insight checks average class method cyclomatic complexity, the lower the score the easier your code is to understand. It raises an issue if complexity is over `5.0`.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\ClassMethodAverageCyclomaticComplexityIsHigh`

<details>
    <summary>Configuration</summary>

```php
\NunoMaduro\PhpInsights\Domain\Insights\ClassMethodAverageCyclomaticComplexityIsHigh::class => [
     'maxClassMethodAverageComplexity' => 5.0,
]
```
</details>

## Method Cyclomatic Complexity is high <Badge text="^2.12"/> <Badge text="Complexity" type="warn"/>

This insight checks cyclomatic complexity of your methods, the lower the score the easier your code is to understand. It raises an issue if complexity is over `5`.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\MethodCyclomaticComplexityIsHigh`

<details>
    <summary>Configuration</summary>

```php
\NunoMaduro\PhpInsights\Domain\Insights\MethodCyclomaticComplexityIsHigh::class => [
     'maxMethodComplexity' => 5,
]
```
</details>

<!--
Insight template
##  <Badge text="^1.0"/> <Badge text="Complexity" type="warn"/>

This sniff

**Insight Class**: ``

<details>
    <summary>Configuration</summary>

```php

```
</details>
-->
