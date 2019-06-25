<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
final class Style extends SymfonyStyle
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    public $output;

    /**
     * Style constructor.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($this->input = $input, $this->output = $output);
    }

    /**
     * Waits for Enter key.
     *
     * @param  string  $category
     *
     * @return \NunoMaduro\PhpInsights\Application\Console\Style
     */
    public function waitForKey(string $category): Style
    {
        $stdin = fopen('php://stdin', 'r');

        if ($stdin !== false && $this->output instanceof ConsoleOutput && $this->input->isInteractive() === true) {
            $this->newLine();
            $section = $this->output->section();
            $section->writeln(sprintf('<title>Press enter to see %s issues...</title>', strtolower($category)));
            fgetc($stdin);
            $section->clear(3);
        }

        return $this;
    }
}
