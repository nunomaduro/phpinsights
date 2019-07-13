<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Domain\Differ;
use NunoMaduro\PhpInsights\Domain\EcsContainer;
use NunoMaduro\PhpInsights\Domain\FileFactory;
use NunoMaduro\PhpInsights\Domain\FixerFileProcessor;
use NunoMaduro\PhpInsights\Domain\Reflection;
use NunoMaduro\PhpInsights\Domain\SniffFileProcessor;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor as EcsFixerFileProcessor;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor as EcsSniffFileProcessor;

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
            SniffFileProcessor::class => static function () use ($container) {
                $reflection = new Reflection($container->get(EcsSniffFileProcessor::class));

                $fixer = $reflection->get('fixer');
                $errorAndDiffCollector = $reflection->get('errorAndDiffCollector');
                $appliedCheckersCollector = $reflection->get('appliedCheckersCollector');
                /** @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle */
                $easyCodingStandardStyle = $container->get(EasyCodingStandardStyle::class);
                /** @var \Symplify\EasyCodingStandard\Skipper $skipper */
                $skipper = $container->get(Skipper::class);

                return new SniffFileProcessor(
                    $fixer,
                    new FileFactory(
                        $fixer,
                        $errorAndDiffCollector,
                        $skipper,
                        $appliedCheckersCollector,
                        $easyCodingStandardStyle
                    )
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
