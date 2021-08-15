<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Util\Common;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

final class File extends BaseFile
{
    /**
     * @var array<array<\NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator>>
     */
    private array $tokenListeners = [];

    private SniffDecorator $activeSniff;

    private SplFileInfo $fileInfo;

    private bool $isFixable;

    private bool $fixEnabled = false;

    public function __construct(string $path, string $content, Config $config, Ruleset $ruleset)
    {
        $this->content = $content;

        $this->eolChar = Common::detectLineEndings($content);

        parent::__construct($path, $ruleset, $config);
    }

    public function process(): void
    {
        $this->parse();
        $this->fixer->startFile($this);

        foreach ($this->tokens as $stackPtr => $token) {
            if (! isset($this->tokenListeners[$token['code']])) {
                continue;
            }

            /** @var \NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator $sniff */
            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                $this->activeSniff = $sniff;

                try {
                    $sniff->process($this, $stackPtr);
                } catch (Throwable $e) {
                    $this->addError('Unparsable php code: syntax error or wrong phpdocs.', $stackPtr, $token['code']);
                }
            }
        }
    }

    /**
     * @param array<array<\NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator>> $tokenListeners
     */
    public function processWithTokenListenersAndFileInfo(
        array $tokenListeners,
        SplFileInfo $fileInfo,
        bool $isFixable
    ): void {
        $this->tokenListeners = $tokenListeners;
        $this->fileInfo = $fileInfo;
        $this->isFixable = $isFixable;

        $this->process();
    }

    /**
     * Get's the file info from the file.
     */
    public function getFileInfo(): SplFileInfo
    {
        return $this->fileInfo;
    }

    /**
     * Enable fix mode. It's used to prevent report twice
     * details because fixer relaunch process method.
     */
    public function enableFix(): void
    {
        $this->fixEnabled = true;
    }

    public function disableFix(): void
    {
        $this->fixEnabled = false;
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
        $message = $data !== [] ? vsprintf($message, $data) : $message;

        if ($isFixable && $this->isFixable) {
            if ($this->fixEnabled) {
                $this->activeSniff->addFileFixed($this->fileInfo->getRelativePathname());
            } else {
                $this->fixableCount++;
            }

            return true;
        }

        if ($this->fixEnabled) {
            // detail already added
            return true;
        }

        $this->activeSniff->addDetails(
            Details::make()
                ->setLine($line)
                ->setMessage($message)
                ->setFile($this->path)
        );

        return true;
    }
}
