<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use InvalidArgumentException;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Exceptions\DirectoryNotFoundException;
use NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalFilesRepository;
use SebastianBergmann\PHPLOC\Publisher;
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
     * Creates a new instance of Feedback Factory.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository  $filesRepository
     */
    public function __construct(FilesRepository $filesRepository)
    {
        $this->filesRepository = $filesRepository;
    }

    /**
     * @param  array  $metrics
     * @param  string  $dir
     *
     * @return \NunoMaduro\PhpInsights\Domain\Insights\Feedback
     *
     * @throws \ReflectionException
     */
    public function get(array $metrics, string $dir): Feedback
    {
        try {
            $files = array_map(function (SplFileInfo $file) {
                return $file->getRealPath();
            }, iterator_to_array($this->filesRepository->in($dir)->getFiles()));
        } catch (InvalidArgumentException $e) {
            throw new DirectoryNotFoundException($e->getMessage());
        }

        $collector = (new Analyser())->analyse($files);
        $publisher = $collector->getPublisher();

        $metrics = array_filter($metrics, function ($metricClass) {
            return class_exists($metricClass) && array_key_exists(HasInsights::class, class_implements($metricClass));
        });

        $insights = [];
        foreach ($metrics as $metricClass) {
            $metric = new $metricClass();

            $insights = array_merge($insights, array_map(function ($insightClass) use ($dir, $collector, $publisher) {
                return new $insightClass($this->filesRepository->in($dir), $collector, $publisher);
            }, $metric->getInsights($publisher)));
        }

        return new Feedback($publisher, $insights);
    }
}
