<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Exceptions;

use RuntimeException;
use Throwable;

final class PhpStanRuleUnresolvable extends RuntimeException
{
    public static function argument(string $insightClass, string $name, Throwable $previous): self
    {
        return new self(
            "Cannot resolve argument [{$name}] for rule [{$insightClass}].",
            $previous->getCode(),
            $previous
        );
    }
}
