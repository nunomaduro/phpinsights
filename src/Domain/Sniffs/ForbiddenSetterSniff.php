<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;

final class ForbiddenSetterSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Setters are not allowed. Use constructor injection and behavior naming instead.';

    /**
     * @var string
     */
    private const SETTER_REGEX = '#^set[A-Z0-9]#';

    public function register() : array
    {
        return [T_FUNCTION];
    }

    public function process(File $file, $position): void
    {
        $methodName = $file->getDeclarationName($position);
        if ($methodName === null) {
            return;
        }

        $className = $this->getClassName($file);

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

    private function getClassName(File $file): string
    {
        $classTokenPosition = $file->findNext(T_CLASS, 0);

        // anonymous class
        if (! is_integer($classTokenPosition)) {
            return 'anonymous';
        }

        $className = ClassHelper::getFullyQualifiedName($file, $classTokenPosition);

        return ltrim($className, '\\');
    }

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
