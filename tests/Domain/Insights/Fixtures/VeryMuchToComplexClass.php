<?php

namespace Tests\Domain\Insights\Fixtures;


class VeryMuchToComplexClass
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
        if ($second > 1) {
            return $second;
        }
        return $this->second($first, $second);
    }

    public function second(int $first, int $second)
    {
        if ($second >= $first) {
            if ($second == $first) {
                return $first;
            }
            if ($second > 1) {
                return $second;
            }
            return 1;
        }
        if ($first > 1) {
            return $first;
        }
        return 1;
    }

    public function third(int $first, int $second)
    {
        if ($second >= $first) {
            if ($second == $first) {
                return $first;
            }
            if ($second > 1) {
                return $second;
            }
            return 1;
        }
        if ($first > 1) {
            return $first;
        }
        return $this->third($first, $second);
    }
}
