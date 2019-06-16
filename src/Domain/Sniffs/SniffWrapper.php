<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Sniffs;

use Illuminate\Support\Arr;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * This wrapper is meant for us to add custom logic which is applied to all
 * sniffs.
 *
 * This logic could be for example to exclude the sniff if it's running on
 * a specific class.
 */
final class SniffWrapper implements Sniff
{
    /** @var Sniff */
    private $sniff;

    /**
     * Defines a array of files which the sniff should ignore.
     *
     * @var array<string>
     */
    public $ignoreFiles = [];

    /**
     * SniffWrapper constructor.
     *
     * @param Sniff $sniff
     */
    public function __construct(Sniff $sniff)
    {
        $this->sniff = $sniff;
    }

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return mixed[]
     *
     * @see    Tokens.php
     */
    public function register()
    {
        return $this->sniff->register();
    }

    /**
     * Called when one of the token types that this sniff is listening for
     * is found.
     *
     * @param File $file        The PHP_CodeSniffer file
     * @param int $stackPtr The position in the PHP_CodeSniffer file's
     *                      token stack
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    public function process(File $file, $stackPtr)
    {
        dd("herer");
        // skip files if they are part of ignore files array.
        if ($this->skipFilesFromIgnoreFiles($file)) {
            return;
        }

       return $this->sniff->process($file, $stackPtr);
    }

    private function skipFilesFromIgnoreFiles(File $file): bool
    {
        foreach ($this->ignoreFiles as $ignoreFile) {
            if (self::pathsAreEqual($ignoreFile, $file->getFilename())) {
                return  true;
            }
        }
        return false;
    }

    private static function pathsAreEqual(string $pathA, string $pathB): bool
    {
        return realpath($pathA) === realpath($pathB);
    }
}
