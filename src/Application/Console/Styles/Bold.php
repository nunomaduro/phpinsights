<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Styles;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Style;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class Bold implements Style
{
    /**
     * {@inheritdoc}
     */
    public function addTo(OutputInterface $output): void
    {
        $outputStyle = new OutputFormatterStyle('default', 'default', ['bold']);

        $output->getFormatter()->setStyle('bold', $outputStyle);
    }
}
