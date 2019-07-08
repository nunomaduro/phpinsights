<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

/**
 * @internal
 */
final class FileProcessor implements FileProcessorInterface
{
    /**
     * @var array<\PHP_CodeSniffer\Sniffs\Sniff|\PHPStan\Rules\Rule>
     */
    private $checkers = [];

    /**
     * @var array<array<\NunoMaduro\PhpInsights\Domain\Sniffs\SniffDecorator>>
     */
    private $tokenListeners = [];

    /**
     * @var \PHP_CodeSniffer\Fixer
     */
    private $fixer;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\FileFactory
     */
    private $fileFactory;

    /**
     * @var \PHPStan\Analyser\Analyser
     */
    private $analyser;

    /**
     * FileProcessor constructor.
     *
     * @param  \PHP_CodeSniffer\Fixer  $fixer
     * @param  \NunoMaduro\PhpInsights\Domain\FileFactory  $fileFactory
     */
    public function __construct(Fixer $fixer, FileFactory $fileFactory)
    {
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
    }

    public function addSniff(Sniff $sniff): void
    {
        $this->checkers[] = $sniff;

        foreach ($sniff->register() as $token) {
            $this->tokenListeners[$token][] = $sniff;
        }
    }

    /**
     * @return array<\PHP_CodeSniffer\Sniffs\Sniff|\PHPStan\Rules\Rule>
     */
    public function getCheckers(): array
    {
        return $this->checkers;
    }

    public function processFile(SmartFileInfo $smartFileInfo): string
    {
        $file = $this->fileFactory->createFromFileInfo($smartFileInfo);
        $file->processWithTokenListenersAndFileInfo(
            $this->tokenListeners,
            $smartFileInfo
        );

        if ($smartFileInfo->getRealPath() !== false) {
            $this->analyser->analyse([$smartFileInfo->getRealPath()], true);
        }

        return $this->fixer->getContents();
    }

    public function setAnalyser(\PHPStan\Analyser\Analyser $analyser): void
    {
        $this->analyser = $analyser;
    }
}
