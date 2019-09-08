<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Util\Common;
use Symfony\Component\Finder\SplFileInfo;

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
     */
    public function __construct(string $path, string $content)
    {
        $this->content = $content;

        $this->eolChar = Common::detectLineEndings($content);

        $config = new Config([], false);
        $config->__set('tabWidth', 4);
        $config->__set('annotations', false);
        $config->__set('encoding', 'UTF-8');

        parent::__construct(
            $path,
            new Ruleset($config),
            $config
        );
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

                try {
                    @$sniff->process($this, $stackPtr);
                } catch (\Throwable $e) {
                    $this->addError('Unparsable php code: syntax error or wrong phpdocs.', $stackPtr, $token['code']);
                }
            }
        }

        $this->fixedCount += $this->fixer->getFixCount();
    }

    public function getErrorCount(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): array
    {
        return [];
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
    ): bool {
        $message = count($data) > 0 ? vsprintf($message, $data) : $message;

        $this->activeSniff->addDetails(
            Details::make()
                ->setLine($line)
                ->setMessage($message)
                ->setFile($this->path)
        );

        return true;
    }
}
