<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PHPStan\Rules\Registry;

final class PhpStanRulesRegistry extends Registry
{
    /**
     * @param array<\NunoMaduro\PhpInsights\Domain\Insights\PhpStanRuleDecorator> $rules
     *
     * @throws \ReflectionException
     */
    public function addRules(array $rules): void
    {
        $reflection = new Reflection($this);
        $loadedRules = $reflection->get('rules') ?? [];

        foreach ($rules as $rule) {
            $loadedRules[$rule->getNodeType()][] = $rule;
        }

        $reflection->set('rules', $loadedRules);
    }
}
