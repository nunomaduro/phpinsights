<?php


namespace Tests\Domain\Insights\Fixtures;


class ClassWithHighMethodAverageComplexity
{

    public function first(int $first)
    {
        if ($first === 1) {
            return 1;
        }

        if ($first === 2) {
            return 2;
        }

        if ($first === 3) {
            return 3;
        }

        if ($first === 4) {
            return 4;
        }

        if ($first === 5) {
            return 5;
        }

        return $first;
    }

    public function second(int $second)
    {
        if ($second === 1) {
            return 1;
        }

        if ($second === 2) {
            return 2;
        }

        if ($second === 3) {
            return 3;
        }

        return $second;
    }

    public function third(int $third)
    {
        if ($third === 1) {
            return 1;
        }

        if ($third === 2) {
            return 2;
        }

        if ($third === 3) {
            return 3;
        }

        if ($third === 4) {
            return 4;
        }

        if ($third === 5) {
            return 5;
        }

        return $third;
    }
}
