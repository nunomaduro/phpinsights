<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Domain\Differ;
use NunoMaduro\PhpInsights\Domain\FileFactory;
use NunoMaduro\PhpInsights\Domain\FileProcessors\FixerFileProcessor;
use NunoMaduro\PhpInsights\Domain\FileProcessors\RectorFileProcessor;
use NunoMaduro\PhpInsights\Domain\FileProcessors\SniffFileProcessor;
use NunoMaduro\PhpInsights\Domain\ParserFactory;

/**
 * @internal
 */
final class FileProcessors
{
    /**
     * Injects file processors into the container definitions.
     *
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            SniffFileProcessor::class => static fn (): SniffFileProcessor => new SniffFileProcessor(
                new FileFactory()
            ),
            FixerFileProcessor::class => static fn (): FixerFileProcessor => new FixerFileProcessor(
                new Differ()
            ),
            RectorFileProcessor::class => static fn (): RectorFileProcessor => new RectorFileProcessor(
                new Differ(),
                ParserFactory::createParser(),
                ParserFactory::getLexer(),
            ),
        ];
    }
}
