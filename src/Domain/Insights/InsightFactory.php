<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\PhpStanContainer;
use NunoMaduro\PhpInsights\Domain\Runner;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;
use PHPStan\Rules\Rule as RuleContract;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class InsightFactory
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    private $filesRepository;

    /**
     * @var string
     */
    private $dir;

    /**
     * @var array<string>
     */
    private $insightsClasses;

    /**
     * @var array<\NunoMaduro\PhpInsights\Domain\Insights\RuleDecorator>
     */
    private $rules;

    /**
     * @var array<SniffDecorator>
     */
    private $sniffs;

    /** @var bool */
    private $ran = false;

    /**
     * Creates a new instance of Insight Factory
     *
     * @param \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository $filesRepository
     * @param string $dir
     * @param array<string> $insightsClasses
     */
    public function __construct(FilesRepository $filesRepository, string $dir, array $insightsClasses)
    {
        $this->filesRepository = $filesRepository;
        $this->dir = $dir;
        $this->insightsClasses = $insightsClasses;
    }

    /**
     * Creates a Insight from the given error class.
     *
     * @param string $errorClass
     * @param array<string, array> $config
     * @param OutputInterface $consoleOutput
     *
     * @return InsightContract
     *
     * @throws \Exception
     */
    public function makeFrom(
        string $errorClass,
        array $config,
        OutputInterface $consoleOutput
    ): InsightContract
    {
        switch (true) {
            case array_key_exists(SniffContract::class, class_implements($errorClass)):
                $this->runInsightCollector($config, $consoleOutput);

                /** @var SniffDecorator $sniff */
                foreach ($this->sniffs as $sniff) {
                    if ($sniff->getInsightClass() === $errorClass) {
                        return $sniff;
                    }
                }

                throw new RuntimeException("The rule has been removed somehow. This shouldn't happen.");

            case array_key_exists(RuleContract::class, class_implements($errorClass)):
                $this->runInsightCollector($config, $consoleOutput);

                /** @var \NunoMaduro\PhpInsights\Domain\Insights\RuleDecorator $rule */
                foreach ($this->rules as $rule) {
                    if ($rule->getInsightClass() === $errorClass) {
                        return $rule;
                    }
                }
                throw new RuntimeException("The rule has been removed somehow. This shouldn't happen.");

            default:
                throw new RuntimeException(sprintf('Insight `%s` is not instantiable.', $errorClass));
        }
    }

    /**
     * Returns the Sniffs PHP CS classes from the given array of Metrics.
     *
     * @param array<string> $insights
     * @param array<string, array> $config
     *
     * @return array<SniffDecorator>
     */
    public function sniffsFrom(array $insights, array $config): array
    {
        $sniffs = [];

        foreach ($insights as $insight) {
            if (array_key_exists(SniffContract::class, class_implements($insight))) {
                /** @var \PHP_CodeSniffer\Sniffs\Sniff $sniff */
                $sniff = new $insight();

                foreach ($config['config'][$insight] ?? [] as $property => $value) {
                    $sniff->{$property} = $value;
                }

                $sniffs[] = new SniffDecorator(
                    $sniff,
                    $this->dir
                );
            }
        }

        return $sniffs;
    }

    /**
     * Returns the phpstan rule classes from the given array of Metrics.
     *
     * @param array<string> $insights
     *
     * @return array<\NunoMaduro\PhpInsights\Domain\Insights\RuleDecorator>
     */
    public function rulesFrom(array $insights): array
    {
        $rules = [];
        $container = PhpStanContainer::make();

        foreach ($insights as $insight) {
            if (array_key_exists(RuleContract::class, class_implements($insight))) {
                /** @var \PHPStan\Rules\Rule $rule */
                $rule = $container->createInstance($insight);
                $rule = new RuleDecorator($rule);

                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * @param array<string, mixed> $config
     * @param \Symfony\Component\Console\Output\OutputInterface $consoleOutput
     *
     * @throws \ReflectionException
     */
    private function runInsightCollector(
        array $config,
        OutputInterface $consoleOutput
    ): void
    {
        if ($this->ran === true) {
            return;
        }

        $runner = new Runner(
            $consoleOutput,
            $this->filesRepository
        );

        // Add php stan rules
        $rules = $this->rulesFrom($this->insightsClasses);
        $this->rules = $rules;
        $runner->addRules($rules);

        // Add php cs sniffs
        $sniffs = $this->sniffsFrom($this->insightsClasses, $config);
        $this->sniffs = $sniffs;
        $runner->addSniffs($sniffs);

        // Run it.
        $runner->run();
        $this->ran = true;
    }
}
