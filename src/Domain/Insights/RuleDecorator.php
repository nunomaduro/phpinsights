<?php

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

class RuleDecorator implements Insight, Rule
{
    /** @var \PHPStan\Rules\Rule */
    private $rule;

    private $errors = [];

    /**
     * RuleDecorator constructor.
     *
     * @param \PHPStan\Rules\Rule $rule
     */
    public function __construct(\PHPStan\Rules\Rule $rule)
    {
        $this->rule = $rule;
    }

    /**
     * Checks if the insight detects an issue.
     *
     * @return bool
     */
    public function hasIssue(): bool
    {
        return ! empty($this->errors);
    }

    /**
     * Gets the title of the insight.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return "rule fail";
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
     * @return array<string|\PHPStan\Rules\RuleError> errors
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errors = $this->rule->processNode($node, $scope);

        $this->errors += $errors;

        return $errors;
    }
}
