<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\FileProcessors;

use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor;
use NunoMaduro\PhpInsights\Domain\FileFactory;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class SniffFileProcessor implements FileProcessor
{
    /**
     * @var array<\PHP_CodeSniffer\Sniffs\Sniff|\PHPStan\Rules\Rule>
     */
    private $checkers = [];

    /**
     * @var array<array<\NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator>>
     */
    private $tokenListeners = [];

    /**
     * @var \NunoMaduro\PhpInsights\Domain\FileFactory
     */
    private $fileFactory;

    /**
     * FileProcessor constructor.
     *
     * @param \NunoMaduro\PhpInsights\Domain\FileFactory $fileFactory
     */
    public function __construct(FileFactory $fileFactory)
    {
        $this->fileFactory = $fileFactory;
    }

    public function addSniff(Sniff $sniff): void
    {
        $this->checkers[] = $sniff;

        foreach ($sniff->register() as $token) {
            $this->tokenListeners[$token][] = $sniff;
        }
    }

    public function processFile(SplFileInfo $splFileInfo): void
    {
        $file = $this->fileFactory->createFromFileInfo($splFileInfo);
        $file->processWithTokenListenersAndFileInfo(
            $this->tokenListeners,
            $splFileInfo
        );
    }
}
