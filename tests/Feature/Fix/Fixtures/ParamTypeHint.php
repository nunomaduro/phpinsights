<?php

declare(strict_types=1);

/**
 * This test class is for testing if fix for Sniff is correctly applied
 */
final class ParamTypeHint
{
    /**
     * Do some calculation
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function sum($a, $b)
    {
        return $a + $b;
    }
}
