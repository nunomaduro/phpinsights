# Complexity

The Complexity section is for now only one Insight in one Metric:

* `NunoMaduro\PhpInsights\Domain\Metrics\Complexity\Complexity` <Badge text="Complexity" type="warn" vertical="middle"/>

## Cyclomatic Complexity is high <Badge text="^1.0"/> <Badge text="Complexity" type="warn"/>

This insight checks complexity cyclomatic on your classes. It raises an issue if complexity is over `5`.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh`

<!--
Insight template
##  <Badge text="^1.0"/> <Badge text="Complexity" type="warn"/>

This sniff

**Insight Class**: ``

<details>
    <summary>Configuration</summary>

```php
\NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class => [
     'maxComplexity' => 5,
]
```
</details>
-->
