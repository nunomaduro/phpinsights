<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Domain\FileFactory;
use NunoMaduro\PhpInsights\Domain\PhpStanContainer;
use NunoMaduro\PhpInsights\Infrastructure\FileProcessors\PhpStanFileProcessor;
use NunoMaduro\PhpInsights\Infrastructure\FileProcessors\SniffFileProcessor;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Parser\Parser;

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
                return new SniffFileProcessor(
                    new FileFactory()
                );
            },
            PhpStanFileProcessor::class => static function () {
                $container = PhpStanContainer::make();

                /** @var ScopeFactory $scopeFactory */
                $scopeFactory = $container->getByType(ScopeFactory::class);
                /** @var Parser $parser */
                $parser = $container->getByType(Parser::class);
                /** @var NodeScopeResolver $resolver */
                $resolver = $container->getByType(NodeScopeResolver::class);

                return new PhpStanFileProcessor(
                    $scopeFactory,
                    $parser,
                    $resolver
                );
            },
        ];
    }
}
