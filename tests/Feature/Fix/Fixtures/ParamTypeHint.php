<?php

declare(strict_types=1);

/**
 * This test class is for testing if fix for Sniff is correctly applied
 */
final class ParamTypeHint
{
    /**
     * Do some calculation
     */
    public function sum(int $a, int $b): int
    {
        return $a + $b;
    }
}
