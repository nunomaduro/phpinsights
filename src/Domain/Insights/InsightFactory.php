<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Runner;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
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
     * @var array<SniffDecorator>
     */
    private $sniffs = [];

    /**
     * @var array<\NunoMaduro\PhpInsights\Domain\Insights\FixerDecorator>
     */
    private $fixers = [];

    /** @var bool */
    private $ran = false;

    /**
     * Creates a new instance of Insight Factory.
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
    ): InsightContract {
        $this->runInsightCollector($config, $consoleOutput);

        switch (true) {
            case $this->isClassImplementInterface($errorClass, SniffContract::class):
                /** @var SniffDecorator $sniff */
                foreach ($this->sniffs as $sniff) {
                    if ($sniff->getInsightClass() === $errorClass) {
                        return $sniff;
                    }
                }

                throw new RuntimeException(sprintf(
                    'The sniff "%s" has been removed somehow. This shouldn\'t happen.',
                    $errorClass
                ));
            case $this->isClassImplementInterface($errorClass, FixerInterface::class):
                /** @var \NunoMaduro\PhpInsights\Domain\Insights\FixerDecorator $fixer */
                foreach ($this->fixers as $fixer) {
                    if ($fixer->getInsightClass() === $errorClass) {
                        return $fixer;
                    }
                }
                throw new RuntimeException(sprintf(
                    'The fixer "%s" has been removed somehow. This shouldn\'t happen.',
                    $errorClass
                ));
            default:
                throw new RuntimeException(sprintf('Insight `%s` is not instantiable.', $errorClass));
        }
    }

    /**
     * @param array<string, mixed> $config
     * @param \Symfony\Component\Console\Output\OutputInterface $consoleOutput
     */
    private function runInsightCollector(
        array $config,
        OutputInterface $consoleOutput
    ): void {
        if ($this->ran === true) {
            return;
        }

        $runner = new Runner(
            $consoleOutput,
            $this->filesRepository
        );

        // Add php cs sniffs
        $sniffs = $this->sniffsFrom($this->insightsClasses, $config);
        $fixers = $this->fixersFrom($this->insightsClasses, $config);
        $this->sniffs = $sniffs;
        $this->fixers = $fixers;

        $runner->addInsights($sniffs);
        $runner->addInsights($fixers);

        // Run it.
        $runner->run();
        $this->ran = true;
    }

    /**
     * Returns the Sniffs PHP CS classes from the given array of Metrics.
     *
     * @param array<string> $insights
     * @param array<string, array> $config
     *
     * @return array<SniffDecorator>
     */
    private function sniffsFrom(array $insights, array $config): array
    {
        $sniffs = [];

        foreach ($insights as $insight) {
            if ($this->isClassImplementInterface($insight, SniffContract::class)) {
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
     * Returns the PHP_CS_Fixers classes from the given array of Metrics.
     *
     * @param array<string> $insights
     * @param array<string, array> $config
     *
     * @return array<\NunoMaduro\PhpInsights\Domain\Insights\FixerDecorator>
     */
    private function fixersFrom(array $insights, array $config): array
    {
        $fixers = [];

        foreach ($insights as $insight) {
            if ($this->isClassImplementInterface($insight, FixerInterface::class)) {
                $fixer = new $insight();

                $excludeConfig = [];
                $insightConfig = $config['config'][$insight] ?? null;

                if (isset($insightConfig['exclude'])) {
                    $excludeConfig = $insightConfig['exclude'];
                    unset($insightConfig['exclude']);
                }

                if ($fixer instanceof ConfigurableFixerInterface && $insightConfig !== null) {
                    $fixer->configure($insightConfig);
                }

                $fixers[] = new FixerDecorator($fixer, $this->dir, $excludeConfig);
            }
        }

        return $fixers;
    }

    private function isClassImplementInterface(string $class, string $interface): bool
    {
        return array_key_exists($interface, class_implements($class));
    }
}
