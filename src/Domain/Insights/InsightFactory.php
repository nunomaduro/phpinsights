<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\EcsContainer;
use NunoMaduro\PhpInsights\Domain\FileProcessor;
use NunoMaduro\PhpInsights\Domain\PhpstanContainer;
use NunoMaduro\PhpInsights\Domain\Reflection;
use NunoMaduro\PhpInsights\Domain\Sniffs\SniffDecorator;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;
use PHPStan\Analyser\Analyser;
use PHPStan\Rules\Registry;
use PHPStan\Rules\Rule as RuleContract;
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
    private $sniffCollector;

    /**
     * @var array<\NunoMaduro\PhpInsights\Domain\Insights\RuleDecorator>
     */
    private $rules;

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
     * @return \NunoMaduro\PhpInsights\Domain\Insights\Sniff
     */
    public function makeFrom(string $errorClass, array $config)
    {
        switch (true) {
            case array_key_exists(SniffContract::class, class_implements($errorClass)):
                return new Sniff($this->getSniffErrors($this->getSniffCollector($config), $errorClass));

            case array_key_exists(RuleContract::class, class_implements($errorClass)):
                $this->getSniffCollector($config);

                /** @var \NunoMaduro\PhpInsights\Domain\Insights\RuleDecorator $rule */
                foreach ($this->rules as $rule) {
                    if ($rule->getInsightClass() === $errorClass) {
                        return $rule;
                    }
                }
            default:
                throw new \RuntimeException(sprintf('Insight `%s` is not instantiable.', $errorClass));
        }
    }

    /**
     * Returns the Sniffs PHP CS classes from the given array of Metrics.
     *
     * @param  array<string>  $insights
     * @param  array<string, array>  $config
     *
     * @return array<\PHP_CodeSniffer\Sniffs\Sniff>
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

                $sniffs[] = $sniff;
            }
        }

        return $sniffs;
    }

    /**
     * Returns the phpstan rule classes from the given array of Matrics.
     *
     * @param  array<string>  $insights
     * @param  array<string, array>  $config
     *
     * @return array<\PHPStan\Rules\Rule>
     */
    public function rulesFrom(array $insights, array $config): array
    {
        $rules = [];

        foreach ($insights as $insight) {
            if (array_key_exists(RuleContract::class, class_implements($insight))) {
                /** @var \PHPStan\Rules\Rule $rule */
                $rule = new RuleDecorator(new $insight());

                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Returns the Error with of the given $sniff, if any.
     *
     * @param  \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector  $collector
     * @param  string  $sniff
     *
     * @return array<\Symplify\EasyCodingStandard\Error\Error>
     */
    private function getSniffErrors(ErrorAndDiffCollector $collector, string $sniff): array
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
    private function getSniffCollector(array $config): ErrorAndDiffCollector
    {
        if ($this->sniffCollector !== null) {
            return $this->sniffCollector;
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

        $sniffer = Container::make()->get(FileProcessor::class);
        foreach ($this->sniffsFrom($this->insightsClasses, $config) as $sniff) {
            $sniffer->addSniff(new SniffDecorator($sniff, $this->dir));
        }

        $phpstanContainer = PhpstanContainer::make($this->dir);
        $string = (string) "test";

        $rules = $this->rulesFrom($this->insightsClasses, $config);
        $phpstanContainer->removeService('registry');
        $phpstanContainer->addService(
            'registry',
            new Registry($rules)
        );
        $this->rules = $rules;


        /** @var Analyser $analyser */
        $analyser = $phpstanContainer->getByType(Analyser::class);
        $sniffer->setAnalyser($analyser);

        /** @var \Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication $application */
        $application = $ecsContainer->get(EasyCodingStandardApplication::class);
        $reflection = new Reflection($application);

        /** @var \Symplify\EasyCodingStandard\Contract\Application\FileProcessorCollectorInterface $fileProcessorCollector */
        $fileProcessorCollector = $reflection->set('fileProcessors', [])
            ->get('singleFileProcessor');

        $fileProcessorCollector->addFileProcessor($sniffer);
        $application->addFileProcessor($sniffer);

        $application->run();

        /** @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector $errorAndDiffCollector */
        $errorAndDiffCollector = $ecsContainer->get(ErrorAndDiffCollector::class);

        // Destroy the container, so insights doesn't fail on consecutive
        // runs. This is needed for tests also.
        $ecsContainer->reset();

        return $this->sniffCollector = $errorAndDiffCollector;
    }
}
