<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use League\Container\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\Exceptions\PhpStanRuleUnresolvable;
use NunoMaduro\PhpInsights\Domain\Insights\PhpStanRuleDecorator;
use NunoMaduro\PhpInsights\Domain\PhpStanContainer;
use PHPStan\DependencyInjection\ParameterNotFoundException;
use PHPStan\Rules\Rule;
use ReflectionClass;

final class PhpStanRuleLoader implements InsightLoader
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function support(string $insightClass): bool
    {
        return array_key_exists(Rule::class, class_implements($insightClass));
    }

    public function load(string $insightClass, string $dir, array $config): Insight
    {
        /** @var PhpStanContainer $phpStanContainer */
        $phpStanContainer = $this->container->get(PhpStanContainer::class);

        $constructor = (new ReflectionClass($insightClass))->getConstructor();

        // Star by checking if we even have a constructor
        if ($constructor === null) {
            return new PhpStanRuleDecorator(
                $this->container->get($insightClass)
            );
        }

        $constructorParameters = $constructor->getParameters();

        // And if the constructor has parameters.
        if ($constructorParameters === []) {
            return new PhpStanRuleDecorator(
                $this->container->get($insightClass)
            );
        }

        $parameters = $this->ResolveParameters(
            $constructorParameters,
            $config,
            $phpStanContainer,
            $insightClass
        );

        return new PhpStanRuleDecorator(
            new $insightClass(
                ...$parameters
            )
        );
    }

    /**
     * @param array<\ReflectionParameter> $constructorParameters
     * @param array<string, int|string|array> $config
     *
     * @return array<mixed>
     */
    private function ResolveParameters(
        array $constructorParameters,
        array $config,
        PhpStanContainer $phpStanContainer,
        string $insightClass
    ): array {
        $parameters = [];

        /** @var \ReflectionParameter $constructorParameter */
        foreach ($constructorParameters as $constructorParameter) {
            $name = $constructorParameter->getName();

            // Get the parameter from config
            if (in_array($name, $config, true)) {
                $parameters[] = $config[$name];
                continue;
            }

            // Try to resolve the parameter from the containers.
            try {
                $parameters[] = $phpStanContainer->getParameter($name);
            } catch (ParameterNotFoundException $exception) {
                $type = $constructorParameter->getType();

                if ($type === null || $type->isBuiltin()) {
                    throw PhpStanRuleUnresolvable::argument(
                        $insightClass,
                        $name,
                        $exception
                    );
                }

                $parameters[] = $this->container->get(
                    $type->getName()
                );
            }
        }
        return $parameters;
    }
}
