<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Style;
use NunoMaduro\PhpInsights\Application\Console\Styles\Bold;
use NunoMaduro\PhpInsights\Application\Console\Styles\Title;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class OutputDecorator
{
    private const STYLES = [
        Title::class,
        Bold::class,
    ];

    /**
     * Decorates the given output with styles.
     */
    public static function decorate(OutputInterface $output): OutputInterface
    {
        foreach (self::STYLES as $styleClass) {
            /** @var Style $style */
            $style = new $styleClass();

            $style->addTo($output);
        }

        return $output;
    }
}
