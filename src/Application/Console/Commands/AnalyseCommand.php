<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use NunoMaduro\PhpInsights\Application\Console\Analyser;
use NunoMaduro\PhpInsights\Application\Console\OutputDecorator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
final class AnalyseCommand
{
    /**
     * Holds an instance of the Analyser.
     *
     * @var \NunoMaduro\PhpInsights\Application\Console\Analyser
     */
    private $analyser;

    /**
     * Creates a new instance of the Analyse Command.
     *
     * @param  \NunoMaduro\PhpInsights\Application\Console\Analyser  $analyser
     */
    public function __construct(Analyser $analyser)
    {
        $this->analyser = $analyser;
    }

    /**
     * Handle the given input.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return void
     */
    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $style = new SymfonyStyle($input, OutputDecorator::decorate($output));

        $this->analyser->analyse($style, [$this->getDirectory($input)]);
    }

    /**
     * Gets the directory from the given input.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     *
     * @return string
     */
    private function getDirectory(InputInterface $input): string
    {
        /** @var string $directory */
        $directory = $input->getArgument('directory');

        if ($directory[0] === DIRECTORY_SEPARATOR) {
            $directory = getcwd() . DIRECTORY_SEPARATOR . $directory;
        }

        return $directory;
    }
}
