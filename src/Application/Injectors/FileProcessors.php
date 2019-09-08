<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;
use NunoMaduro\PhpInsights\Domain\CachedFileLoader;
use NunoMaduro\PhpInsights\Domain\Differ;
use NunoMaduro\PhpInsights\Domain\FileFactory;
use NunoMaduro\PhpInsights\Domain\FileProcessors\FixerFileProcessor;
use NunoMaduro\PhpInsights\Domain\FileProcessors\SniffFileProcessor;

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
            SniffFileProcessor::class => static function () {
                return new SniffFileProcessor(
                    new FileFactory()
                );
            },
            FixerFileProcessor::class => static function () {
                return new FixerFileProcessor(
                    new Differ()
                );
            },
        ];
    }
}
