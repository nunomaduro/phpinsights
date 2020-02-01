<?php

declare(strict_types=1);

namespace Tests\Fixtures\Domain\Fixer;

use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Application\ConfigResolver;

abstract class UnorderedUse
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Analyser
     */
    protected $analyser;

    /**
     * @var \NunoMaduro\PhpInsights\Application\ConfigResolver
     */
    protected $configResolver;

    public function __construct(
        Analyser $analyser,
        ConfigResolver $configResolver
    ) {
        $this->configResolver = $configResolver;
        $this->analyser = $analyser;
    }
}
