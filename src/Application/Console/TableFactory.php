<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class TableFactory
{
    /**
     * Creates a new instance of Table.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  array  $rows
     *
     * @return \Symfony\Component\Console\Helper\Table
     */
    public static function make(OutputInterface $output, array $rows): Table
    {
        $style = (clone Table::getStyleDefinition('symfony-style-guide'))
            ->setCellHeaderFormat('<info>%s</info>')
            ->setHorizontalBorderChars('');

        return (new Table($output))
            ->setStyle($style)
            ->setRows($rows);
    }
}
