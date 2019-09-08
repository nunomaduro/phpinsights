<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Runner;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;
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
    private $sniffs;

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
    ): InsightContract
    {
        $collector = $this->getCollector($config);
        switch (true) {
            case array_key_exists(SniffContract::class, class_implements($errorClass)):
                $this->runInsightCollector($config, $consoleOutput);

                /** @var SniffDecorator $sniff */
                foreach ($this->sniffs as $sniff) {
                    if ($sniff->getInsightClass() === $errorClass) {
                        return $sniff;
                    }
                }

                throw new RuntimeException("The sniff has been removed somehow. This shouldn't happen.");
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
    public function insightsFrom(array $insights, array $config, string $interface): array
    {
        $collectedInsights = [];

        foreach ($insights as $insight) {
            if (array_key_exists($interface, class_implements($insight))) {
                /** @var SniffContract|FixerInterface $currentInsight */
                $currentInsight = new $insight();

                if (isset($config['config'][$insight])) {
                    $this->configureInsight($currentInsight, $config['config'][$insight]);
                }

                $sniffs[] = new SniffDecorator(
                    $sniff,
                    $this->dir
                );
            }
        }

        return $collectedInsights;
    }

    /**
     * @param array<string, mixed> $config
     * @param \Symfony\Component\Console\Output\OutputInterface $consoleOutput
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

        // Add php cs sniffs
        $sniffs = $this->sniffsFrom($this->insightsClasses, $config);
        $this->sniffs = $sniffs;
        $runner->addSniffs($sniffs);

        // Run it.
        $runner->run();
        $this->ran = true;
    }

    /**
     * @param SniffContract|\PhpCsFixer\Fixer\FixerInterface $insight
     * @param array<string, array|string|int|bool> $config
     */
    private function configureInsight($insight, array $config): void
    {
        if ($insight instanceof SniffContract) {
            foreach ($config ?? [] as $property => $value) {
                $insight->{$property} = $value;
            }
            return;
        }

        if ($insight instanceof ConfigurableFixerInterface) {
            $insight->configure($config);
            return;
        }
    }
}
