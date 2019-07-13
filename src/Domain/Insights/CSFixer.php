<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use Symplify\EasyCodingStandard\Error\Error;

/**
 * @internal
 */
final class CSFixer implements InsightContract, HasDetails
{
    /**
     * The errors are from the same type.
     *
     * @var array<\Symplify\EasyCodingStandard\Error\Error>
     */
    private $errors;

    /**
     * Creates a new instance of Sniff Insight
     *
     * @param array<\Symplify\EasyCodingStandard\Error\Error> $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function hasIssue(): bool
    {
        return count($this->errors) > 0;
    }

    public function getTitle(): string
    {
        $sniffClass = $this->getInsightClass();
        $path = explode('\\', $sniffClass);
        $name = (string) array_pop($path);
        $name = str_replace('Fixer', '', $name);

        return ucfirst(mb_strtolower(trim((string) preg_replace('/(?<!\ )[A-Z]/', ' $0', $name))));
    }

    public function getInsightClass(): string
    {
        return explode('.', $this->errors[0]->getSourceClass())[0];
    }

    /**
     * Returns the details of the insight.
     *
     * @return array<string>
     */
    public function getDetails(): array
    {
        return array_map(static function (Error $error) {
            return $error->getFileInfo()->getRealPath() . ':' . $error->getLine() . ': ' . $error->getMessage();
        }, $this->errors);
    }
}
