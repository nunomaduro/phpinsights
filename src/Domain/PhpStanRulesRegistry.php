<?php

namespace NunoMaduro\PhpInsights\Domain;

use PHPStan\Rules\Registry;

class PhpStanRulesRegistry extends Registry
{
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
