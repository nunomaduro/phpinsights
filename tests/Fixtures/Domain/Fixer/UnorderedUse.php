<?php

declare(strict_types=1);

namespace Tests\Fixtures\Domain\Fixer;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Domain\Analyser;

abstract class UnorderedUse
{
    protected Analyser $analyser;
    protected ConfigResolver $configResolver;

    public function __construct(
        Analyser $analyser,
        ConfigResolver $configResolver
    ) {
        $this->configResolver = $configResolver;
        $this->analyser = $analyser;
    }
}
