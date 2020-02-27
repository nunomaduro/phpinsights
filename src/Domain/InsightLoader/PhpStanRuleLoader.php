<?php

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\Insights\PhpStanRuleDecorator;
use PHPStan\Rules\Rule;

final class PhpStanRuleLoader implements InsightLoader
{
    public function support(string $insightClass): bool
    {
        return array_key_exists(Rule::class, class_implements($insightClass));
    }

    public function load(string $insightClass, string $dir, array $config): Insight
    {
        /** @var Rule $rule */
        $rule = new $insightClass();

        return new PhpStanRuleDecorator(
            $rule
        );
        // TODO: Implement load() method.
    }
}
