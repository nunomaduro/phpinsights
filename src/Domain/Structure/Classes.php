<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class Classes implements HasValue
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getClasses());
    }
}
