<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * This sniff disallows setter methods.
 */
#[\AllowDynamicProperties]
final class ForbiddenSetterSniff implements Sniff
{
    private const ERROR_MESSAGE = <<<'EOD'
    Setters are not allowed. Use constructor injection and behavior naming instead.
    EOD;

    private const SETTER_REGEX = '#^set[A-Z0-9]#';

    /**
     * @var array<string>
     */
    public array $allowedMethodRegex;

    public function register(): array
    {
        return [T_FUNCTION];
    }

    /**
     * Runs the sniff on a file.
     *
     * @param int  $position
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function process(File $file, $position): void
    {
        $methodName = $file->getDeclarationName($position);
        if ($methodName === null) {
            return;
        }

        // Check if method should be skipped.
        if ($this->shouldSkip($methodName)) {
            return;
        }

        // Check if method is a setter
        if (preg_match(self::SETTER_REGEX, $methodName) !== 1) {
            return;
        }

        $file->addError(self::ERROR_MESSAGE, $position, self::class);
    }

    /**
     * Checks if we should skip this method based on either the
     * method name or the class name.
     */
    private function shouldSkip(string $methodName): bool
    {
        // Skip setUp method as often used in test classes
        if ($methodName === 'setUp') {
            return true;
        }

        foreach ($this->getAllowedMethodRegex() as $methodRegex) {
            if (preg_match($methodRegex, $methodName) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string>
     */
    private function getAllowedMethodRegex(): array
    {
        return $this->allowedMethodRegex ?? [];
    }
}
