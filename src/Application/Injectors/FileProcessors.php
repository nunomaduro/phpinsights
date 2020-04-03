<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Domain\Differ;
use NunoMaduro\PhpInsights\Domain\FileFactory;
use NunoMaduro\PhpInsights\Domain\FileProcessors\FixerFileProcessor;
use NunoMaduro\PhpInsights\Domain\FileProcessors\SniffFileProcessor;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;

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
            SniffFileProcessor::class => static function (): SniffFileProcessor {
                $config = new Config([], false);
                $config->__set('tabWidth', 4);
                $config->__set('annotations', false);
                $config->__set('encoding', 'UTF-8');

                return new SniffFileProcessor(
                    new FileFactory(
                        new Ruleset($config),
                        $config
                    )
                );
            },
            FixerFileProcessor::class => static function (): FixerFileProcessor {
                return new FixerFileProcessor(
                    new Differ()
                );
            },
        ];
    }
}
