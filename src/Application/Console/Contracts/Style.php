<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Contracts;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
interface Style
{
    /**
     * Adds the style to the given input.
     */
    public function addTo(OutputInterface $output): void;
}
