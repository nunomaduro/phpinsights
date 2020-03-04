<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\FileProcessors;

use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Insights\PhpStanRuleDecorator;
use PHPStan\Analyser\Analyser;
use PHPStan\DependencyInjection\Container as PhpStanContainer;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

final class PhpStanRuleFileProcessor implements FileProcessor
{
    /** @var Analyser */
    private $analyser;

    public function __construct(PhpStanContainer $container)
    {
        $this->analyser = $container->getByType(Analyser::class);
    }

    public function support(Insight $insight): bool
    {
        return $insight instanceof PhpStanRuleDecorator;
    }

    public function addChecker(Insight $insight): void
    {
        if (! $insight instanceof PhpStanRuleDecorator) {
            throw new RuntimeException(sprintf(
                'Unable to add %s, not a PhpStan Rule instance',
                get_class($insight)
            ));
        }
    }

    public function processFile(SplFileInfo $splFileInfo): void
    {
        $path = $splFileInfo->getRealPath();

        if ($path === false) {
            return;
        }

        $this->analyser->analyse(
            [$path],
            false
        );
    }
}
