<?php

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Details;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\FileRuleError;
use PHPStan\Rules\LineRuleError;
use PHPStan\Rules\Rule;

final class PhpStanRuleDecorator implements Rule, Insight, HasDetails
{
    /** @var Rule */
    private $rule;

    /** @var array<Details> */
    private $errors = [];

    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
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

    /**
     * Convert a error to a detail
     *
     * @param \PhpParser\Node $node
     * @param \PHPStan\Analyser\Scope $scope
     * @param \PHPStan\Rules\RuleError|string $error
     *
     * @return \NunoMaduro\PhpInsights\Domain\Details
     *
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

    public function getTitle(): string
    {
        $ruleClass = $this->getInsightClass();

        $path = explode('\\', $ruleClass);
        $name = (string) array_pop($path);

        $name = explode('Rule', $name, 2)[0];

        return ucfirst(
            trim(
                strtolower(
                    (string) preg_replace(
                        '/(?<! )[A-Z]/',
                        ' $0',
                        $name
                    )
                )
            )
        );
    }

    public function getInsightClass(): string
    {
        return get_class($this->rule);
    }

    public function getNodeType(): string
    {
        return $this->rule->getNodeType();
    }

    public function processNode(Node $node, Scope $scope) : array
    {
        $ruleErrors = $this->rule->processNode($node, $scope);

        $errors = [];

        /** @var \PHPStan\Rules\RuleError|string $error */
        foreach ($ruleErrors as $error) {

            $errors[] = self::errorToDetail($node, $scope, $error);
        }
        $this->errors = array_merge($this->errors, $errors);

        return $ruleErrors;
    }
}
