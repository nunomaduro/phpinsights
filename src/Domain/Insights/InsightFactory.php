<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\EcsContainer;
use NunoMaduro\PhpInsights\Domain\FixerFileProcessor;
use NunoMaduro\PhpInsights\Domain\Reflection;
use NunoMaduro\PhpInsights\Domain\SniffFileProcessor;
use NunoMaduro\PhpInsights\Domain\Sniffs\SniffDecorator;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Finder\SourceFinder;

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
     * @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector|null
     */
    private $collector;

    /**
     * Creates a new instance of Insight Factory
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository  $filesRepository
     * @param  string  $dir
     * @param  array<string>  $insightsClasses
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
     * @param  string  $errorClass
     * @param  array<string, array>  $config
     *
     * @return \NunoMaduro\PhpInsights\Domain\Contracts\Insight
     */
    public function makeFrom(string $errorClass, array $config): \NunoMaduro\PhpInsights\Domain\Contracts\Insight
    {
        $collector = $this->getCollector($config);
        switch (true) {
            case array_key_exists(SniffContract::class, class_implements($errorClass)):
                return new Sniff($this->getErrors($collector, $errorClass));
                break;

            case array_key_exists(FixerInterface::class, class_implements($errorClass)):
                return new CSFixer($this->getErrors($collector, $errorClass));
                break;

            default:
                throw new \RuntimeException(sprintf('Insight `%s` is not instantiable.', $errorClass));
                break;
        }
    }

    /**
     * Returns the Sniffs PHP CS classes from the given array of Metrics.
     *
     * @param array<string> $insights
     * @param array<string, array> $config
     * @param string $interface
     *
     * @return array<SniffContract|FixerInterface>
     */
    public function insightsFrom(array $insights, array $config, string $interface): array
    {
        $collectedInsights = [];

        foreach ($insights as $insight) {
            if (array_key_exists($interface, class_implements($insight))) {
                /** @var SniffContract|FixerInterface $currentInsight */
                $currentInsight = new $insight();

                foreach ($config['config'][$insight] ?? [] as $property => $value) {
                    $currentInsight->{$property} = $value;
                }

                $collectedInsights[] = $currentInsight;
            }
        }

        return $collectedInsights;
    }

    /**
     * Returns the Error with of the given $sniff, if any.
     *
     * @param  \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector  $collector
     * @param  string  $sniff
     *
     * @return array<\Symplify\EasyCodingStandard\Error\Error>
     */
    private function getErrors(ErrorAndDiffCollector $collector, string $sniff): array
    {
        $errors = [];

        foreach ($collector->getErrors() as $errorsPerFile) {
            foreach ($errorsPerFile as $error) {
                if (strpos($error->getSourceClass(), $sniff) !== false) {
                    $key = $this->getSniffKey($error);
                    if (! array_key_exists($key, $errors)) {
                        $errors[$key] = $error;
                    }
                }
            }
        }

        return array_values($errors);
    }

    /**
     * Gets a key from a Error.
     *
     * @param  \Symplify\EasyCodingStandard\Error\Error  $error
     *
     * @return string
     */
    private function getSniffKey(Error $error): string
    {
        return sprintf('%s||%s||%s||%s',
            $error->getFileInfo()->getRealPath(),
            $error->getSourceClass(),
            $error->getLine(),
            $error->getMessage()
        );
    }

    /**
     * @param  array<string, array>  $config
     *
     * @return \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector
     */
    private function getCollector(array $config): ErrorAndDiffCollector
    {
        if ($this->collector !== null) {
            return $this->collector;
        }

        $reflection = new Reflection($configuration = new Configuration());
        $reflection->set('sources', [$this->dir])
            ->set('shouldClearCache', true)
            ->set('showProgressBar', true);

        $ecsContainer = EcsContainer::make();

        $ecsContainer->set(Configuration::class, $configuration);

        /** @var \Symplify\EasyCodingStandard\Finder\SourceFinder $sourceFinder */
        $sourceFinder = $ecsContainer->get(SourceFinder::class);
        $sourceFinder->setCustomSourceProvider($this->filesRepository);

        $phpinsightsContainer = Container::make();
        $sniffer = $phpinsightsContainer->get(SniffFileProcessor::class);
        foreach ($this->insightsFrom($this->insightsClasses, $config, SniffContract::class) as $sniff) {
            if (! $sniff instanceof SniffContract) {
                continue;
            }
            $sniffer->addSniff(new SniffDecorator($sniff, $this->dir));
        }

        $csFixer = $phpinsightsContainer->get(FixerFileProcessor::class);
        foreach ($this->insightsFrom($this->insightsClasses, $config, FixerInterface::class) as $checker) {
            $csFixer->addChecker($checker);
        }
        $fileProcessors = [$sniffer, $csFixer];

        /** @var \Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication $application */
        $application = $ecsContainer->get(EasyCodingStandardApplication::class);
        $reflection = new Reflection($application);

        /** @var \Symplify\EasyCodingStandard\Contract\Application\FileProcessorCollectorInterface $fileProcessorCollector */
        $fileProcessorCollector = $reflection->set('fileProcessors', [])
            ->get('singleFileProcessor');

        foreach ($fileProcessors as $fileProcessor) {
            $fileProcessorCollector->addFileProcessor($fileProcessor);
            $application->addFileProcessor($fileProcessor);
        }

        $application->run();

        /** @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector $errorAndDiffCollector */
        $errorAndDiffCollector = $ecsContainer->get(ErrorAndDiffCollector::class);

        // Destroy the container, so we insights doesn't fail on consecutive
        // runs. This is needed for tests also.
        $ecsContainer->reset();

        return $this->collector = $errorAndDiffCollector;
    }
}
