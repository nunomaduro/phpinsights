<?php

namespace NunoMaduro\PhpInsights\Domain;

use PHPStan\Rules\Registry;

class PhpStanRulesRegistry extends Registry
{
    public function addRules(array $rules): void
    {

        $reflection = new Reflection($this);
        dd($reflection->get('rules'));

        foreach ($rules as $rule) {
            $this->rules[$rule->getNodeType()][] = $rule;
        }
    }
}
