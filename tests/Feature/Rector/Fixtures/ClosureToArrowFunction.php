<?php

declare(strict_types=1);

namespace Tests\Feature\Rector\Fixtures;

final class ClosureToArrowFunction
{
    public function sum($a, $b): callable
    {
        return static function () use ($a, $b) {
            return  $a + $b;
        };
    }
}
