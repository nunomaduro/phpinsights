<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor;
use NunoMaduro\PhpInsights\Domain\Differ;
use NunoMaduro\PhpInsights\Domain\FileFactory;
use NunoMaduro\PhpInsights\Domain\FileProcessors\FixerFileProcessor;
use NunoMaduro\PhpInsights\Domain\FileProcessors\PhpStanRuleFileProcessor;
use NunoMaduro\PhpInsights\Domain\FileProcessors\SniffFileProcessor;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Parser\Parser;

/**
 * @internal
 */
final class FileProcessorsProvider extends AbstractServiceProvider
{
    protected $provides = [
        SniffFileProcessor::class,
        FixerFileProcessor::class,
        PhpStanRuleFileProcessor::class,
        FileProcessor::FILE_PROCESSOR_TAG
    ];

    public function register()
    {
        $this->getContainer()->add(SniffFileProcessor::class)
            ->addArgument(FileFactory::class)
            ->addTag(FileProcessor::FILE_PROCESSOR_TAG);

        $this->getContainer()->add(FixerFileProcessor::class)
            ->addArgument(Differ::class)
            ->addTag(FileProcessor::FILE_PROCESSOR_TAG);

        $this->getContainer()->add(PhpStanRuleFileProcessor::class)
            ->addArgument(ScopeFactory::class)
            ->addArgument(Parser::class)
            ->addArgument(NodeScopeResolver::class)
            ->addTag(FileProcessor::FILE_PROCESSOR_TAG);
    }
}
