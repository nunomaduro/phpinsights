<?php

namespace Tests\Domain\Insights\Fixtures;

/**
 * @see \Tests\Domain\Insights\ReadonlyClassTest
 */
readonly class ReadonlyClass {
    public function __construct(
        private string $foo,
        private string $bar,
    ) {
    }
}
