<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Infrastructure\FileProcessors;

use NunoMaduro\PhpInsights\Domain\FileFactory;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

/**
 * @internal
 */
final class SniffFileProcessor implements FileProcessorInterface
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

        return $this->fixer->getContents();
    }
}
