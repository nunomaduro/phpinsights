<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Domain\FileFactory;
use NunoMaduro\PhpInsights\Domain\FileProcessors\SniffFileProcessor;

/**
 * @internal
 */
final class FileProcessors
{
    /**
     * Injects repositories into the container definitions.
     *
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        $container = EcsContainer::make();

        return [
            SniffFileProcessor::class => static function () {
                return new SniffFileProcessor(
                    new FileFactory()
                );
            },
            FixerFileProcessor::class => static function () use ($container) {
                $reflection = new Reflection($container->get(EcsFixerFileProcessor::class));
                $cachedFileLoader = $reflection->get('cachedFileLoader');
                $errorAndDiffCollector = $reflection->get('errorAndDiffCollector');
                $fileToTokensParser = $reflection->get('fileToTokensParser');

                $differ = new Differ();
                return new FixerFileProcessor(
                    $cachedFileLoader,
                    $errorAndDiffCollector,
                    $fileToTokensParser,
                    $differ
                );
            },
        ];
    }
}
