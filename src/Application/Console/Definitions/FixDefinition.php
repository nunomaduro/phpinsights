<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Definitions;

use Symfony\Component\Console\Input\InputDefinition;

/**
 * @internal
 */
final class FixDefinition
{
    public static function get(): InputDefinition
    {
        return DefaultDefinition::get();
    }
}
