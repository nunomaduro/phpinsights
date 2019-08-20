<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Sniffs\SniffDecorator;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Util\Common;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class File extends BaseFile
{
    /**
     * @var string|null
     */
    private $activeSniffClass;

    /**
     * @var string|null
     */
    private $previousActiveSniffClass;

    /**
     * @var array<array<\NunoMaduro\PhpInsights\Domain\Sniffs\SniffDecorator>>
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
     * @param  string  $path
     * @param  string  $content
     * @param  \PHP_CodeSniffer\Fixer  $fixer
     * @param  \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector  $errorAndDiffCollector
     * @param  \Symplify\EasyCodingStandard\Skipper  $skipper
     * @param  \Symplify\EasyCodingStandard\Application\AppliedCheckersCollector  $appliedCheckersCollector
     * @param  \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle  $easyCodingStandardStyle
     */
    public function __construct(
        string $path,
        string $content,
        Fixer $fixer,
        ErrorAndDiffCollector $errorAndDiffCollector,
        Skipper $skipper,
        AppliedCheckersCollector $appliedCheckersCollector,
        EasyCodingStandardStyle $easyCodingStandardStyle
    )
    {
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

    public function process(): void
    {
        $this->parse();
        $this->fixer->startFile($this);

        foreach ($this->tokens as $stackPtr => $token) {
            if (isset($this->tokenListeners[$token['code']]) === false) {
                continue;
            }

            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                if ($this->skipper->shouldSkipCheckerAndFile($sniff, $this->fileInfo)) {
                    continue;
                }

                $this->reportActiveSniffClass($sniff);

                try {
                    $sniff->process($this, $stackPtr);
                } catch (\Throwable $e) {
                    $this->addError('Unparsable php code: syntax error or wrong phpdocs.', $stackPtr, $token['code']);
                }
            }
        }

        $this->fixedCount += $this->fixer->getFixCount();
    }

    public function getErrorCount(): int
    {
        throw new \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): array
    {
        throw new \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException();
    }

    /**
     * {@inheritdoc}
     */
    public function addFixableError($error, $stackPtr, $code, $data = [], $severity = 0): bool
    {
        $this->appliedCheckersCollector->addFileInfoAndChecker(
            $this->fileInfo,
            $this->resolveFullyQualifiedCode((string) $code)
        );

        return $this->addError($error, $stackPtr, $code, $data, $severity);
    }

    /**
     * @param array<array<\NunoMaduro\PhpInsights\Domain\Sniffs\SniffDecorator>> $tokenListeners
     */
    public function processWithTokenListenersAndFileInfo(array $tokenListeners, SmartFileInfo $fileInfo): void
    {
        $this->tokenListeners = $tokenListeners;
        $this->fileInfo = $fileInfo;
        $this->process();
    }

    /**
     * Get's the file info from the file.
     *
     * @return SmartFileInfo
     */
    public function getFileInfo(): SmartFileInfo
    {
        return $this->fileInfo;
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
    ): bool
    {
        $message = count($data) > 0 ? vsprintf($message, $data) : $message;

        $this->errorAndDiffCollector->addErrorMessage(
            $this->fileInfo,
            $line,
            $message,
            $this->resolveFullyQualifiedCode((string) $sniffClassOrCode)
        );

        return true;
    }

    /**
     * @param SniffDecorator $sniff
     */
    private function reportActiveSniffClass(SniffDecorator $sniff): void
    {
        // used in other places later
        $this->activeSniffClass = get_class($sniff->getSniff());

        if (! $this->easyCodingStandardStyle->isDebug()) {
            return;
        }

        if ($this->previousActiveSniffClass === $this->activeSniffClass) {
            return;
        }

        $this->easyCodingStandardStyle->writeln($this->activeSniffClass);
        $this->previousActiveSniffClass = $this->activeSniffClass;
    }

    /**
     * @param  string  $sniffClassOrCode
     *
     * @return string
     */
    private function resolveFullyQualifiedCode(string $sniffClassOrCode): string
    {
        if (class_exists($sniffClassOrCode)) {
            return $sniffClassOrCode;
        }

        return $this->activeSniffClass . '.' . $sniffClassOrCode;
    }
}
