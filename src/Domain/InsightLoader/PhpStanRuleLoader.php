<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use League\Container\Container;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\PhpStanRegistryFactory;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPStan\Rules\Registry;
use PHPStan\Rules\RegistryFactory;
use PHPStan\Rules\Rule;

final class PhpStanRuleLoader implements InsightLoader
{
    /** @var Container */
    private $container;

    /** @var array<class-string, array<string, int|string|array>> */
    private $rules = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function support(string $insightClass): bool
    {
        return array_key_exists(Rule::class, class_implements($insightClass));
    }

    /**
     * Loads a insight.
     *
     * @param class-string $insightClass
     * @param array<string, int|string|array> $config Related to $insightClass
     */
    public function load(string $insightClass, string $dir, array $config): void
    {
        $this->rules[$insightClass] = $config;
    }

    /**
     * Get all loaded insights.
     *
     * @return array<\NunoMaduro\PhpInsights\Domain\Contracts\Insight>
     */
    public function getLoadedInsights(): array
    {
        $phpStan = $this->createContainer();
        $this->container->add(\PHPStan\DependencyInjection\Container::class, $phpStan);

        return $phpStan->getByType(PhpStanRegistryFactory::class)->getRules();
    }

    private function createContainer(): \PHPStan\DependencyInjection\Container
    {
        $phpStanConfig = [
            'parameters' => [
                'customRulesetUsed' => true,
            ],
            'services' => [
                'registry' => [
                    'class' => Registry::class,
                    'factory' => '@registryFactory::create',
                ],
                'registryFactory' => [
                    'class' => PhpStanRegistryFactory::class,
                ],
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

        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        return (new ContainerFactory($configuration->getDirectory()))->create(
            sys_get_temp_dir() . '/phpstan',
            array_merge(
                [$phpStanConfig],
                $configuration->getNeonFiles()
            ),
            []
        );
    }
}
