<?php


namespace Tests\Domain\Insights\Fixtures;


class ClassWithComplexMethod
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
}
