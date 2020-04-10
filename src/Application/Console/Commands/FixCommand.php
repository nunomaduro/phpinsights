<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use NunoMaduro\PhpInsights\Application\Console\Formatters\Console;
use NunoMaduro\PhpInsights\Application\Console\OutputDecorator;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory;
use NunoMaduro\PhpInsights\Domain\MetricsFinder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class FixCommand
{
    private InsightCollectionFactory $collectionFactory;

    public function __construct(
        InsightCollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $metrics = MetricsFinder::find();
        $collection = $this->collectionFactory->get($metrics, $output);

        $output = OutputDecorator::decorate($output);
        $formatter = new Console($input, $output);

        $formatter->formatFix($collection, $metrics);

        return 0;
    }
}
