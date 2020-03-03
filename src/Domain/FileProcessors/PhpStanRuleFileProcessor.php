<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\FileProcessors;

use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Insights\PhpStanRuleDecorator;
use PHPStan\Analyser\FileAnalyser;
use PHPStan\Rules\Registry;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

final class PhpStanRuleFileProcessor implements FileProcessor
{
    /** @var PhpStanRuleDecorator[] */
    private $insights = [];

    /** @var \PHPStan\Rules\Registry */
    private $registry = null;

    /** @var \PHPStan\Analyser\FileAnalyser */
    private $analyser;

    public function __construct(FileAnalyser $analyser)
    {
        $this->analyser = $analyser;
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

        $this->insights[] = $insight;
    }

    public function processFile(SplFileInfo $splFileInfo): void
    {
        if ($this->registry === null) {
            $this->registry = new Registry($this->insights);
        }

        $path = $splFileInfo->getRealPath();

        if ($path === false) {
            return;
        }

        $this->analyser->analyseFile(
            $path,
            $this->registry,
            null
        );
    }
}
