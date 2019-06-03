<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;

/**
 * This sniff disallows setter methods
 */
final class ForbiddenSetterSniff implements Sniff
{
    private const ERROR_MESSAGE = 'Setters are not allowed. Use constructor injection and behavior naming instead.';

    private const SETTER_REGEX = '#^set[A-Z0-9]#';

    /**
     * @var array<string>
     */
    public $allowedMethodRegex;

    /**
     * @inheritDoc
     */
    public function register() : array
    {
        return [T_FUNCTION];
    }

    /**
     * @inheritDoc
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $methodName = $file->getDeclarationName($position);
        if ($methodName === null) {
            return;
        }

        $className = self::getClassName($file);

        // Check if method should be skipped.
        if ($this->shouldSkip($methodName, $className)) {
            return;
        }

        // Check if method is a setter
        if (preg_match(self::SETTER_REGEX, $methodName) !== 1) {
            return;
        }

        $file->addError(self::ERROR_MESSAGE, $position, self::class);
    }

    /**
     * Returns the class name from the file.
     *
     * @param File $file
     * @return string
     */
    private static function getClassName(File $file): string
    {
        $classTokenPosition = $file->findNext(T_CLASS, 0);

        // anonymous class
        if (! is_integer($classTokenPosition)) {
            return 'anonymous';
        }

        $className = ClassHelper::getFullyQualifiedName($file, $classTokenPosition);

        return ltrim($className, '\\');
    }

    /**
     * Checks if we should skip this method based on either the
     * method name or the class name.
     *
     * @param string $methodName
     * @param string $className
     * @return bool
     */
    private function shouldSkip(string $methodName, string $className): bool
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
