<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use InvalidArgumentException;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Exceptions\DirectoryNotFoundException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class FeedbackFactory
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    private $filesRepository;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Analyser
     */
    private $analyser;

    /**
     * Creates a new instance of Feedback Factory.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository  $filesRepository
     * @param  \NunoMaduro\PhpInsights\Domain\Analyser  $analyser
     * @param  array<string, int|string>  $config
     */
    public function __construct(FilesRepository $filesRepository, Analyser $analyser)
    {
        $this->filesRepository = $filesRepository;
        $this->analyser = $analyser;
    }

    /**
     * @param  array  $metrics
     * @param  array  $config
     * @param  string  $dir
     *
     * @return \NunoMaduro\PhpInsights\Domain\Insights\Feedback
     *
     */
    public function get(array $metrics, array $config, string $dir): Feedback
    {
        try {
            $files = array_map(function (SplFileInfo $file) {
                return $file->getRealPath();
            }, iterator_to_array($this->filesRepository->in($dir)->getFiles()));
        } catch (InvalidArgumentException $e) {
            throw new DirectoryNotFoundException($e->getMessage());
        }

        $collector = $this->analyser->analyse($files);

        $metrics = array_filter($metrics, function ($metricClass) {
            return class_exists($metricClass);
        });

        $insights = [];
        foreach ($metrics as $metricClass) {
            $insights[$metricClass] = array_map(function ($insightClass) use ($collector, $config) {
                return new $insightClass($collector, $config['config'][$insightClass] ?? []);
            }, $this->getInsights($metricClass, $config));
        }

        return new Feedback($collector, $insights);
    }


    /**
     * Returns the `Insights` from the given metric class.
     *
     * @param  string  $metricClass
     * @param  array  $config
     *
     * @return array
     */
    private function getInsights(string $metricClass, array $config): array
    {
        $metric = new $metricClass;

        $insights = array_key_exists(HasInsights::class, class_implements($metricClass)) ? $metric->getInsights() : [];

        $insights = array_merge($insights, $config['add'][$metricClass] ?? []);

        return array_diff($insights, $config['remove'] ?? []);
    }
}
