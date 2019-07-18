<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Runner;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;
use PHPStan\Rules\Rule as RuleContract;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Error\Error;

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
                $this->runErrorCollector($config, $consoleOutput);

                return new Sniff(
                    $this->getSniffErrors($errorClass)
                );

            case array_key_exists(RuleContract::class, class_implements($errorClass)):
                $this->runErrorCollector($config, $consoleOutput);

                /** @var \NunoMaduro\PhpInsights\Domain\Insights\RuleDecorator $rule */
                foreach ($this->rules as $rule) {
                    if ($rule->getInsightClass() === $errorClass) {
                        return $rule;
                    }
                }
                throw new RuntimeException("The rule has been removed somehow. This shouldn't happen.");

            default:
                throw new \RuntimeException(sprintf('Insight `%s` is not instantiable.', $errorClass));
        }
    }

    /**
     * Returns the Sniffs PHP CS classes from the given array of Metrics.
     *
     * @param array<string> $insights
     * @param array<string, array> $config
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
     * Returns the phpstan rule classes from the given array of Metrics.
     *
     * @param array<string> $insights
     * @return array<\NunoMaduro\PhpInsights\Domain\Insights\RuleDecorator>
     */
    public function rulesFrom(array $insights): array
    {
        $rules = [];

        foreach ($insights as $insight) {
            if (array_key_exists(RuleContract::class, class_implements($insight))) {
                $rule = new RuleDecorator(new $insight());

                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Returns the Error with of the given $sniff, if any.
     *
     * @param string $sniff
     *
     * @return array<\Symplify\EasyCodingStandard\Error\Error>
     */
    private function getSniffErrors(string $sniff): array
    {
        $errors = [];

        foreach ($this->sniffCollector->getErrors() as $errorsPerFile) {
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
     * @param \Symplify\EasyCodingStandard\Error\Error $error
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

    private function runErrorCollector(
        array $config,
        OutputInterface $consoleOutput
    ): void
    {
        if ($this->sniffCollector !== null) {
            return;
        }

        $runner = new Runner(
            $this->dir,
            $consoleOutput,
            $this->filesRepository
        );

        $rules = $this->rulesFrom($this->insightsClasses);
        $this->rules = $rules;

        // Add php stan rules
        $runner->addRules($rules);

        // Add sniff rules
        $runner->addSniffs($this->sniffsFrom($this->insightsClasses, $config));

        // Run it.
        $runner->run();

        // Collect the errors from sniffs
        $this->sniffCollector = $runner->getSniffErrorCollector();

        // Destroy the container, so insights doesn't fail on consecutive
        // runs. This is needed for tests also.
        $runner->reset();
    }
}
