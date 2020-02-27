<?php

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use PHPStan\Rules\Rule;

final class PhpStanRuleDecorator implements Rule, Insight, HasDetails
{
    /** @var Rule */
    private $rule;

    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
    }

    public function getDetails(): array
    {
        // TODO: Implement getDetails() method.
    }

    public function hasIssue(): bool
    {
        // TODO: Implement hasIssue() method.
    }

    public function getTitle(): string
    {
        // TODO: Implement getTitle() method.
    }

    public function getInsightClass(): string
    {
        // TODO: Implement getInsightClass() method.
    }

    public function getNodeType(): string
    {
        // TODO: Implement getNodeType() method.
    }

    public function processNode(Node $node, Scope $scope): array
    {
        // TODO: Implement processNode() method.
    }
}
