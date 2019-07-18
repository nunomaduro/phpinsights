<?php

namespace NunoMaduro\PhpInsights\Infrastructure\FileProcessors;

use PHPStan\Analyser\Analyser;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

class PhpStanFileProcessor implements FileProcessorInterface
{
    /**
     * @var \PHPStan\Analyser\Analyser
     */
    private $analyser;

    /**
     * PhpStanFileProcessor constructor.
     *
     * @param \PHPStan\Analyser\Analyser $analyser
     */
    public function __construct(Analyser $analyser)
    {
        $this->analyser = $analyser;
    }

    public function getCheckers(): array
    {
        return ["filler"];
    }

    /**
     * @param \Symplify\PackageBuilder\FileSystem\SmartFileInfo $smartFileInfo
     *
     * @return string
     *
     * @throws \Throwable
     */
    public function processFile(SmartFileInfo $smartFileInfo): string
    {
        if ($smartFileInfo->getRealPath() !== false) {
            $this->analyser->analyse(
                [$smartFileInfo->getRealPath()],
                true
            );
        }

        return "";
    }
}
