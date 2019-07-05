<?php

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

class RuleDecorator implements Insight, Rule, HasDetails
{
    /** @var \PHPStan\Rules\Rule */
    private $rule;

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

            $errors[] = Details::make()
                ->withLine($line)
                ->withFile($fileName)
                ->withMessage($message)
                ->withOriginal($error);
        }

        $this->errors += $errors;

        return $ruleErrors;
    }

    /**
     * Returns the details of the insight.
     *
     * @return array<\NunoMaduro\PhpInsights\Domain\Details>
     */
    public function getDetails(): array
    {
        return $this->errors;
    }
}
