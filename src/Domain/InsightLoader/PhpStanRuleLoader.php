<?php

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use League\Container\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\Insights\PhpStanRuleDecorator;
use PHPStan\Rules\Rule;

final class PhpStanRuleLoader implements InsightLoader
{
    /** @var Container */
    private $phpStanContainer;

    public function __construct(Container $phpStanContainer)
    {
        $this->phpStanContainer = $phpStanContainer;
    }


    public function support(string $insightClass): bool
    {
        return array_key_exists(Rule::class, class_implements($insightClass));
    }

    public function load(string $insightClass, string $dir, array $config): Insight
    {
        /** @var Rule $rule */
        $rule = $this->phpStanContainer->get($insightClass);

        return new PhpStanRuleDecorator(
            $rule
        );
    }
}
