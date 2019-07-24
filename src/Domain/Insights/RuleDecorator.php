<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use Illuminate\Support\Str;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Details;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\FileRuleError;
use PHPStan\Rules\LineRuleError;
use PHPStan\Rules\Rule;

final class RuleDecorator implements Insight, Rule, HasDetails
{
    /** @var \PHPStan\Rules\Rule */
    private $rule;

    /** @var array<Details> */
    private $errors = [];

    /**
     * RuleDecorator constructor.
     *
     * @param \PHPStan\Rules\Rule $rule
     */
    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
    }

    /**
     * Convert a error to a detail
     *
     * @param \PhpParser\Node $node
     * @param \PHPStan\Analyser\Scope $scope
     * @param \PHPStan\Rules\RuleError|string $error
     *
     * @return \NunoMaduro\PhpInsights\Domain\Details
     *
     * @throws \PHPStan\ShouldNotHappenException
     */
    private static function errorToDetail(Node $node,
                                          Scope $scope,
                                          $error): Details
    {
        $line = $node->getLine();
        $fileName = $scope->getFileDescription();
        if (is_string($error)) {
            $message = $error;
        } else {
            $message = $error->getMessage();
            if (
                $error instanceof LineRuleError
                && $error->getLine() !== -1
            ) {
                $line = $error->getLine();
            }
            if (
                $error instanceof FileRuleError
                && $error->getFile() !== ''
            ) {
                $fileName = $error->getFile();
            }
        }

        return Details::make()
            ->setLine($line)
            ->setFile($fileName)
            ->setMessage($message)
            ->setOriginal($error);
    }

    /**
     * Checks if the insight detects an issue.
     *
     * @return bool
     */
    public function hasIssue(): bool
    {
        return count($this->errors) !== 0;
    }

    /**
     * Gets the title of the insight.
     *
     * @return string
     */
    public function getTitle(): string
    {
        $ruleClass = $this->getInsightClass();

        $path = explode('\\', $ruleClass);
        $name = (string) array_pop($path);

        $name = Str::before($name, 'Rule');

        return Str::ucfirst(
            trim(
                Str::lower(
                    (string) preg_replace(
                        '/(?<!\ )[A-Z]/',
                        ' $0',
                        $name
                    )
                )
            )
        );
    }

    /**
     * Get the class name of Insight used.
     *
     * @return string
     */
    public function getInsightClass(): string
    {
        return get_class($this->rule);
    }

    /**
     * @return string Class implementing \PhpParser\Node
     */
    public function getNodeType(): string
    {
        return $this->rule->getNodeType();
    }

    /**
     * @param \PhpParser\Node $node
     * @param \PHPStan\Analyser\Scope $scope
     *
     * @return array<string|\PHPStan\Rules\RuleError> errors
     *
     * @throws \PHPStan\ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $ruleErrors = $this->rule->processNode($node, $scope);

        $errors = [];

        /** @var \PHPStan\Rules\RuleError|string $error */
        foreach ($ruleErrors as $error) {

            $errors[] = self::errorToDetail($node, $scope, $error);
        }

        $this->errors += $errors;

        return $ruleErrors;
    }

    /**
     * Returns the details of the insight.
     *
     * @return array<int, \NunoMaduro\PhpInsights\Domain\Details>
     */
    public function getDetails(): array
    {
        return $this->errors;
    }
}
