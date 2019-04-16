<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use Symplify\EasyCodingStandard\Error\Error;

/**
 * @internal
 */
final class Sniff implements Insight, HasDetails
{
    /**
     * Contains the source class error description.
     *
     * @var array<string, string>
     */
    private static $messages = [
        ForbiddenFunctionsSniff::class => '%s forbidden functions',
        UnusedVariableSniff::class => '%s unused variables',
        UnusedPrivateElementsSniff::class => '%s unused private attributes',
    ];

    /**
     * The errors are from the same type.
     *
     * @var \Symplify\EasyCodingStandard\Error\Error[]
     */
    private $errors;

    /**
     * Creates a new instance of Sniff Insight
     *
     * @param  \Symplify\EasyCodingStandard\Error\Error[]  $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * {@inheritDoc}
     */
    public function hasIssue(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle(): string
    {
        $sniffClass = explode('.', $this->errors[0]->getSourceClass())[0];

        if (array_key_exists($sniffClass, self::$messages)) {
            return sprintf(self::$messages[$sniffClass], count($this->errors));
        }

        $path = explode('\\', $sniffClass);
        $name = (string) array_pop($path);

        $name = str_replace('Sniff', '', $name);

        $name = ucfirst(trim(mb_strtolower((string) preg_replace('/(?<!\ )[A-Z]/', ' $0', $name))));

        return count($this->errors) . " $name issues detected";
    }

    /**
     * {@inheritDoc}
     */
    public function getDetails(): array
    {
        return array_map(function (Error $error) {
            return $error->getFileInfo()->getRealPath() . ':' . $error->getLine() . ': ' . $error->getMessage();
        }, $this->errors);
    }
}
