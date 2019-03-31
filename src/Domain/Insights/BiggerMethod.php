<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Analyser;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class BiggerMethod extends Insight
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (int)$this->publisher->getMaximumMethodLength() > 5;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'Having `methods` with more than 5 lines is prohibited';
    }
}
