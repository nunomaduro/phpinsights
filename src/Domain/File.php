<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Util\Common;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;

final class File extends BaseFile
{
    /** @var \NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator */
    private $activeSniff;

    /**
     * @var array<array<\NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator>>
     */
    private $tokenListeners = [];

    /**
     * @var SplFileInfo
     */
    private $fileInfo;

    /**
     * File constructor.
     *
     * @param string $path
     * @param string $content
     * @param \PHP_CodeSniffer\Fixer $fixer
     */
    public function __construct(string $path, string $content, Fixer $fixer)
    {
        $this->path = $path;
        $this->content = $content;
        $this->fixer = $fixer;

        $this->eolChar = Common::detectLineEndings($content);

        $this->config = new Config([], false);
        $this->config->__set('tabWidth', 4);
        $this->config->__set('annotations', false);
        $this->config->__set('encoding', 'UTF-8');
    }

    public function process(): void
    {
        $this->parse();
        $this->fixer->startFile($this);

        foreach ($this->tokens as $stackPtr => $token) {
            if (isset($this->tokenListeners[$token['code']]) === false) {
                continue;
            }

            /** @var \NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator $sniff */
            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                $this->activeSniff = $sniff;

                $sniff->process($this, $stackPtr);
            }
        }

        $this->fixedCount += $this->fixer->getFixCount();
    }

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
    public function addFixableError($error,
                                    $stackPtr,
                                    $code,
                                    $data = [],
                                    $severity = 0): bool
    {
        return $this->addError($error, $stackPtr, $code, $data, $severity);
    }

    /**
     * @param array<array<\NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator>> $tokenListeners
     * @param \Symfony\Component\Finder\SplFileInfo $fileInfo
     */
    public function processWithTokenListenersAndFileInfo(array $tokenListeners,
                                                         SplFileInfo $fileInfo
    ): void
    {
        $this->tokenListeners = $tokenListeners;
        $this->fileInfo = $fileInfo;
        $this->process();
    }

    /**
     * Get's the file info from the file.
     *
     * @return SplFileInfo
     */
    public function getFileInfo(): SplFileInfo
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

        $this->activeSniff->addDetails(
            Details::make()
                ->setLine($line)
                ->setMessage($message)
                ->setFile($this->path)
                ->setSeverity($severity)
        );

        return true;
    }
}
