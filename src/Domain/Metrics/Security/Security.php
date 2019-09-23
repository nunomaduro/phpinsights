<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Security;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;

final class Security implements HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            ForbiddenSecurityIssues::class,
        ];
    }
}
