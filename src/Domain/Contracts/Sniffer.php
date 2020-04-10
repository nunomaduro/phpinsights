<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Domain\Collector;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * @internal
 */
interface Sniffer
{
    /**
     * Returns the PHP CS sniff associated with the sniffer.
     */
    public function getSniff(): Sniff;

    /**
     * Collects the given error.
     *
     * @param  array<string, string>  $error
     */
    public function collect(Collector $collector, array $error): void;
}
