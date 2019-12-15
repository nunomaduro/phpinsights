<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use NunoMaduro\PhpInsights\Application\Console\Formatters\Console;
use NunoMaduro\PhpInsights\Application\Console\OutputDecorator;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory;
use NunoMaduro\PhpInsights\Domain\MetricsFinder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class FixCommand
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory
     */
    private $collectionFactory;
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Configuration
     */
    private $config;

    public function __construct(
        InsightCollectionFactory $collectionFactory,
        Configuration $config
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
    }

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $metrics = MetricsFinder::find();
        $collection = $this->collectionFactory->get($metrics, $output);

        $output = OutputDecorator::decorate($output);
        $formatter = new Console($input, $output);

        $formatter->formatFix($collection, $this->config->getDirectory(), $metrics);

        return 0;
    }
}
