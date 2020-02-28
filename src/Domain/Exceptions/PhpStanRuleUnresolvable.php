<?php

namespace NunoMaduro\PhpInsights\Domain\Exceptions;

use RuntimeException;

class PhpStanRuleUnresolvable extends RuntimeException
{
    public static function argument(string $insightClass, string $name): self
    {
        return new self(
            "Cannot resolve argument [{$name}] for rule [{$insightClass}]."
        );
    }
}
