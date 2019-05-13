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
     * @var \PHP_CodeSniffer\Sniffs\Sniff[]
     */
    private $sniffs = [];

    /**
     * @var \PHP_CodeSniffer\Sniffs\Sniff[][]
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

    /**
     * @param  \PHP_CodeSniffer\Sniffs\Sniff  $sniff
     *
     * @return void
     */
    public function addSniff(Sniff $sniff): void
    {
        $this->sniffs[] = $sniff;

        foreach ($sniff->register() as $token) {
            $this->tokenListeners[$token][] = $sniff;
        }
    }

    /**
     * @return \PHP_CodeSniffer\Sniffs\Sniff[]
     */
    public function getCheckers(): array
    {
        return $this->sniffs;
    }

    /**
     * @param  \Symplify\PackageBuilder\FileSystem\SmartFileInfo  $smartFileInfo
     *
     * @return string
     */
    public function processFile(SmartFileInfo $smartFileInfo): string
    {
        $file = $this->fileFactory->createFromFileInfo($smartFileInfo);
        $file->processWithTokenListenersAndFileInfo($this->tokenListeners, $smartFileInfo);

        return $this->fixer->getContents();
    }
}
