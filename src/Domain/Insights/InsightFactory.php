<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;
use RuntimeException;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

/**
 * @internal
 */
final class InsightFactory
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var string[]
     */
    private $insightsClasses;

    /**
     * @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector|null
     */
    private $sniffCollector;

    /**
     * Creates a new instance of Insight Factory
     *
     * @param  string  $dir
     * @param  array  $insightsClasses
     */
    public function __construct(string $dir, array $insightsClasses)
    {
        $this->dir = $dir;
        $this->insightsClasses = $insightsClasses;
    }

    /**
     * Creates a Insight from the given error class.
     *
     * @param  string  $errorClass
     *
     * @return \NunoMaduro\PhpInsights\Domain\Insights\Sniff
     */
    public function makeFrom(string $errorClass): Sniff
    {
        switch (true) {
            case array_key_exists(SniffContract::class, class_implements($errorClass)):
                return new Sniff($this->getSniffErrors($this->getSniffCollector(), $errorClass));
                break;

            default:
                throw new RuntimeException(sprintf('Insight `%s` is not instantiable.', $errorClass));
                break;
        }
    }

    /**
     * Returns the Sniffs PHP CS classes from the given array of Metrics.
     *
     * @param  string[]  $insights
     *
     * @return string[]
     */
    public function sniffsFrom(array $insights): array
    {
        $sniffs = [];

        foreach ($insights as $insight) {
            if (array_key_exists(SniffContract::class, class_implements($insight))) {
                $sniffs[] = new $insight();
            }
        }

        return $sniffs;
    }

    /**
     * Returns the Error with of the given $sniff, if any.
     *
     * @param  \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector  $collector
     * @param  string  $sniff
     *
     * @return \Symplify\EasyCodingStandard\Error\Error[]
     */
    private function getSniffErrors(ErrorAndDiffCollector $collector, string $sniff): array
    {
        $errors = [];

        foreach ($collector->getErrors() as $errorsPerFile) {

            foreach ($errorsPerFile as $error) {
                if (strpos($error->getSourceClass(), $sniff) !== false) {
                    $errors[] = $error;
                }
            }
        }

        return $errors;
    }

    /**
     * @return \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector
     */
    private function getSniffCollector(): ErrorAndDiffCollector
    {
        if ($this->sniffCollector !== null) {
            return $this->sniffCollector;
        }

        $configuration = new Configuration();
        $reflection = new \ReflectionClass($configuration);
        $property = $reflection->getProperty('shouldClearCache');
        $property->setAccessible(true);
        $property->setValue($configuration, true);

        $property = $reflection->getProperty('sources');
        $property->setAccessible(true);
        $property->setValue($configuration, [$this->dir]);

        $property = $reflection->getProperty('showProgressBar');
        $property->setAccessible(true);
        $property->setValue($configuration, false);

        $property = $reflection->getProperty('configFilePath');
        $property->setAccessible(true);
        // $property->setValue($configuration, 'vendor/symplify/easy-coding-standard/config/clean-code.yml');

        $container = require __DIR__ . '/../../../vendor/symplify/easy-coding-standard/bin/container.php';
        $container->set(Configuration::class, $configuration);

        $sniffer = $container->get(SniffFileProcessor::class);

        foreach (InsightFactory::sniffsFrom($this->insightsClasses) as $sniff) {
            $sniffer->addSniff($sniff);
        }

        $application = $container->get(EasyCodingStandardApplication::class);
        $application->addFileProcessor($sniffer);

        $application->run();

        return $this->sniffCollector = $container->get(ErrorAndDiffCollector::class);
    }
}
