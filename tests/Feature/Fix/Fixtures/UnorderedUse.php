<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Application\ConfigResolver;

/**
 * This test class is for testing if fix from CSFixer is correctly applied
 */
abstract class UnorderedUse
{
    public function __construct(
        Analyser $analyser,
        ConfigResolver $configResolver
    ) {
        echo 'Do nothing';
    }
}
