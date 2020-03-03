<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use League\Container\Container;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\Insights\PhpStanRuleDecorator;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPStan\Rules\RegistryFactory;
use PHPStan\Rules\Rule;

final class PhpStanRuleLoader implements InsightLoader
{
    /** @var Container */
    private $container;

    /** @var array<class-string, array> */
    private $rules = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function support(string $insightClass): bool
    {
        return array_key_exists(Rule::class, class_implements($insightClass));
    }

    public function load(string $insightClass, string $dir, array $config): void
    {
        $this->rules[$insightClass] = $config;
    }

    public function getLoadedInsights(): array
    {
        $phpStanConfig = [
            'parameters' => [
                'customRulesetUsed' => true,
            ],
        ];

        foreach ($this->rules as $rule => $config) {
            // If not parameters, then just pass to rules array in phpStan.
            if ($config === []) {
                $phpStanConfig['rules'][] = $rule;
                continue;
            }

            $phpStanConfig['services'][] = [
                'class' => $rule,
                'tags' => [RegistryFactory::RULE_TAG],
                'arguments' => $config,
            ];
        }

        $phpStan = (new ContainerFactory($this->container->get(Configuration::class)->getDirectory()))->create(
            sys_get_temp_dir() . '/phpstan',
            [
                $phpStanConfig
            ],
            []
        );
        $this->container->add(\PHPStan\DependencyInjection\Container::class, $phpStan);

        $wrapped = [];
        foreach ($phpStan->getServicesByTag(RegistryFactory::RULE_TAG) as $rule) {
            $wrapped[] = new PhpStanRuleDecorator($rule);
        }

        return $wrapped;
    }
}
