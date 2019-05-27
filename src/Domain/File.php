<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Common;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class File extends BaseFile
{
    /**
     * @var string
     */
    public $tokenizerType = 'PHP';

    /**
     * @var \PHP_CodeSniffer\Fixer
     */
    public $fixer;

    /**
     * @var string|null
     */
    private $activeSniffClass;

    /**
     * @var string|null
     */
    private $previousActiveSniffClass;

    /**
     * @var \PHP_CodeSniffer\Sniffs\Sniff[][]
     */
    private $tokenListeners = [];

    /**
     * @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var \Symplify\EasyCodingStandard\Skipper
     */
    private $skipper;

    /**
     * @var \Symplify\EasyCodingStandard\Application\AppliedCheckersCollector
     */
    private $appliedCheckersCollector;

    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var \Symplify\PackageBuilder\FileSystem\SmartFileInfo
     */
    private $fileInfo;

    /**
     * File constructor.
     *
     * @param string                                                             $path
     * @param string                                                             $content
     * @param \PHP_CodeSniffer\Fixer                                             $fixer
     * @param \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector           $errorAndDiffCollector
     * @param \Symplify\EasyCodingStandard\Skipper                               $skipper
     * @param \Symplify\EasyCodingStandard\Application\AppliedCheckersCollector  $appliedCheckersCollector
     * @param \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle
     */
    public function __construct(
        string $path,
        string $content,
        Fixer $fixer,
        ErrorAndDiffCollector $errorAndDiffCollector,
        Skipper $skipper,
        AppliedCheckersCollector $appliedCheckersCollector,
        EasyCodingStandardStyle $easyCodingStandardStyle
    ) {
        $this->path = $path;
        $this->content = $content;
        $this->fixer = $fixer;
        $this->errorAndDiffCollector = $errorAndDiffCollector;

        $this->eolChar = Common::detectLineEndings($content);
        $this->skipper = $skipper;
        $this->appliedCheckersCollector = $appliedCheckersCollector;

        $this->config = new Config([], false);
        $this->config->__set('tabWidth', 4);
        $this->config->__set('annotations', false);
        $this->config->__set('encoding', 'UTF-8');
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    /**
     * {@inheritdoc}
     */
    public function process(): void
    {
        $this->parse();
        $this->fixer->startFile($this);

        foreach ($this->tokens as $stackPtr => $token) {
            if (false === isset($this->tokenListeners[$token['code']])) {
                continue;
            }

            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                if ($this->skipper->shouldSkipCheckerAndFile(
                    $sniff,
                    $this->fileInfo
                )) {
                    continue;
                }

                $this->reportActiveSniffClass($sniff);

                $sniff->process($this, $stackPtr);
            }
        }

        $this->fixedCount += $this->fixer->getFixCount();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCount(): int
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): array
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     */
    public function addFixableError(
        $error,
        $stackPtr,
        $code,
        $data = [],
        $severity = 0
    ): bool {
        $this->appliedCheckersCollector->addFileInfoAndChecker(
            $this->fileInfo,
            $this->resolveFullyQualifiedCode($code)
        );

        return $this->addError($error, $stackPtr, $code, $data, $severity);
    }

    /**
     * @param Sniff[][] $tokenListeners
     */
    public function processWithTokenListenersAndFileInfo(
        array $tokenListeners,
        SmartFileInfo $fileInfo
    ): void {
        $this->tokenListeners = $tokenListeners;
        $this->fileInfo = $fileInfo;
        $this->process();
    }

    /**
     * {@inheritdoc}
     */
    protected function addMessage(
        $isError,
        $message,
        $line,
        $column,
        $sniffClassOrCode,
        $data,
        $severity,
        $isFixable = false
    ): bool {
        $message = count($data) > 0 ? vsprintf($message, $data) : $message;

        $this->errorAndDiffCollector->addErrorMessage(
            $this->fileInfo,
            $line,
            $message,
            $this->resolveFullyQualifiedCode($sniffClassOrCode)
        );

        return true;
    }

    /**
     * @param Sniff $sniff
     */
    private function reportActiveSniffClass(Sniff $sniff): void
    {
        // used in other places later
        $this->activeSniffClass = get_class($sniff);

        if (!$this->easyCodingStandardStyle->isDebug()) {
            return;
        }

        if ($this->previousActiveSniffClass === $this->activeSniffClass) {
            return;
        }

        $this->easyCodingStandardStyle->writeln($this->activeSniffClass);
        $this->previousActiveSniffClass = $this->activeSniffClass;
    }

    /**
     * @param string $sniffClassOrCode
     *
     * @return string
     */
    private function resolveFullyQualifiedCode(string $sniffClassOrCode): string
    {
        if (class_exists($sniffClassOrCode)) {
            return $sniffClassOrCode;
        }

        return $this->activeSniffClass.'.'.$sniffClassOrCode;
    }
}
