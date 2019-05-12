<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Nette\Utils\FileSystem;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Differ\DifferInterface;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
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
     * @var \Symplify\EasyCodingStandard\Configuration\Configuration
     */
    private $configuration;

    /**
     * @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var \PhpCsFixer\Differ\DifferInterface
     */
    private $differ;

    /**
     * @var \Symplify\EasyCodingStandard\Application\AppliedCheckersCollector
     */
    private $appliedCheckersCollector;

    /**
     * FileProcessor constructor.
     *
     * @param  \PHP_CodeSniffer\Fixer  $fixer
     * @param  \NunoMaduro\PhpInsights\Domain\FileFactory  $fileFactory
     * @param  \Symplify\EasyCodingStandard\Configuration\Configuration  $configuration
     * @param  \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector  $errorAndDiffCollector
     * @param  \PhpCsFixer\Differ\DifferInterface  $differ
     * @param  AppliedCheckersCollector  $appliedCheckersCollector
     */
    public function __construct(
        Fixer $fixer,
        FileFactory $fileFactory,
        Configuration $configuration,
        ErrorAndDiffCollector $errorAndDiffCollector,
        DifferInterface $differ,
        AppliedCheckersCollector $appliedCheckersCollector
    )
    {
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->differ = $differ;
        $this->appliedCheckersCollector = $appliedCheckersCollector;
    }

    /**
     * @param  Sniff  $sniff
     */
    public function addSniff(Sniff $sniff): void
    {
        $this->sniffs[] = $sniff;

        foreach ($sniff->register() as $token) {
            $this->tokenListeners[$token][] = $sniff;
        }
    }

    /**
     * @return Sniff[]|DualRunInterface[]
     */
    public function getCheckers(): array
    {

        return $this->sniffs;
    }

    public function processFile(SmartFileInfo $smartFileInfo): string
    {
        $file = $this->fileFactory->createFromFileInfo($smartFileInfo);

        // mimic original behavior
        /** mimics @see \PHP_CodeSniffer\Files\File::process() */
        /** mimics @see \PHP_CodeSniffer\Fixer::fixFile() */
        $this->fixFile($file, $this->fixer, $smartFileInfo, $this->tokenListeners);

        // add diff
        if ($smartFileInfo->getContents() !== $this->fixer->getContents()) {
            $diff = $this->differ->diff($smartFileInfo->getContents(), $this->fixer->getContents());
            $this->errorAndDiffCollector->addDiffForFileInfo(
                $smartFileInfo,
                $diff,
                $this->appliedCheckersCollector->getAppliedCheckersPerFileInfo($smartFileInfo)
            );
        }

        // 4. save file content (faster without changes check)
        if ($this->configuration->isFixer()) {
            FileSystem::write($file->getFilename(), $this->fixer->getContents());
        }

        return $this->fixer->getContents();
    }

    /**
     * @param  Sniff[][]  $tokenListeners
     */
    private function fixFile(File $file, Fixer $fixer, SmartFileInfo $smartFileInfo, array $tokenListeners): void
    {
        $previousContent = $smartFileInfo->getContents();
        $this->fixer->loops = 0;

        do {
            // Only needed once file content has changed.
            $content = $previousContent;

            $file->setContent($content);
            $file->processWithTokenListenersAndFileInfo($tokenListeners, $smartFileInfo);

            // fixed content
            $previousContent = $fixer->getContents();
            ++$this->fixer->loops;
        } while ($previousContent !== $content);
    }
}
