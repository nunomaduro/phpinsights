<?php

namespace Tests\Domain\Insights\Fixtures;

class LittleToComplexClass
{
    public function first(int $first, int $second)
    {
        if ($first >= $second) {
            if ($first == $second) {
                return $second;
            }
            if ($first > 1) {
                return $first;
            }

            return 1;
        }

        return $second;
    }

    public function second(int $first, int $second)
    {
        if ($second >= $first) {
            if ($second == $first) {
                return $first;
            }

            return $second;
        }

        return 1;
    }
}
