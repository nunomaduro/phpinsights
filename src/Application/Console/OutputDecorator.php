<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use NunoMaduro\PhpInsights\Application\Console\Styles\Bold;
use NunoMaduro\PhpInsights\Application\Console\Styles\Title;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class OutputDecorator
{
    /**
     * @var array<string>
     */
    private static $styles = [
        Title::class,
        Bold::class,
    ];

    /**
     * Decorates the given output with styles.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public static function decorate(OutputInterface $output): OutputInterface
    {
        foreach (self::$styles as $styleClass) {
            $style = new $styleClass();

            /** @var \NunoMaduro\PhpInsights\Application\Console\Contracts\Style $style */
            $style->addTo($output);
        }

        return $output;
    }
}
