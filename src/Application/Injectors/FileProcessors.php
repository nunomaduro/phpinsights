<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Domain\EcsContainer;
use NunoMaduro\PhpInsights\Domain\FileFactory;
use NunoMaduro\PhpInsights\Infrastructure\FileProcessors\PhpStanFileProcessor;
use NunoMaduro\PhpInsights\Infrastructure\FileProcessors\SniffFileProcessor;
use NunoMaduro\PhpInsights\Domain\PhpStanContainer;
use NunoMaduro\PhpInsights\Domain\Reflection;
use PHPStan\Analyser\Analyser;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Parser\Parser;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
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
        return [
            SniffFileProcessor::class => static function () {
                $container = EcsContainer::make();

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
            PhpStanFileProcessor::class => static function () {
                $container = PhpStanContainer::make();

                return new PhpStanFileProcessor(
                    $container->getByType(ScopeFactory::class),
                    $container->getByType(Parser::class),
                    $container->getByType(NodeScopeResolver::class)
                );
            }
        ];
    }
}
