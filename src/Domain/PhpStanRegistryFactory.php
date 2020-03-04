<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Insights\PhpStanRuleDecorator;
use PHPStan\DependencyInjection\Container;
use PHPStan\Rules\Registry;
use PHPStan\Rules\RegistryFactory;

final class PhpStanRegistryFactory
{
    /** @var array<PhpStanRuleDecorator> */
    private $rules = [];

    public function __construct(Container $container)
    {
        foreach ($container->getServicesByTag(RegistryFactory::RULE_TAG) as $rule) {
            $this->rules[] = new PhpStanRuleDecorator($rule);
        }
    }

    public function create(): Registry
    {
        return new Registry($this->rules);
    }

    /**
     * @return array<PhpStanRuleDecorator>
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
