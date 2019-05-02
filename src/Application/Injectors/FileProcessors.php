<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Domain\EcsContainer;
use NunoMaduro\PhpInsights\Domain\FileFactory;
use NunoMaduro\PhpInsights\Domain\FileProcessor;
use NunoMaduro\PhpInsights\Domain\Reflection;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

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
        return [
            FileProcessor::class => function () {
                $container = EcsContainer::make();

                $reflection = new Reflection($container->get(SniffFileProcessor::class));

                $fixer = $reflection->get('fixer');
                $configuration = $reflection->get('configuration');
                $errorAndDiffCollector = $reflection->get('errorAndDiffCollector');
                $differ = $reflection->get('differ');
                $appliedCheckersCollector = $reflection->get('appliedCheckersCollector');
                /** @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle */
                $easyCodingStandardStyle = $container->get(EasyCodingStandardStyle::class);
                /** @var \Symplify\EasyCodingStandard\Skipper $skipper */
                $skipper = $container->get(Skipper::class);

                return new FileProcessor(
                    $fixer,
                    new FileFactory(
                        $fixer,
                        $errorAndDiffCollector,
                        $skipper,
                        $appliedCheckersCollector,
                        $easyCodingStandardStyle,
                    ),
                    $configuration,
                    $errorAndDiffCollector,
                    $differ,
                    $appliedCheckersCollector
                );
            },
        ];
    }
}
