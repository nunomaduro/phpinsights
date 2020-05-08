<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
final class Style extends SymfonyStyle
{
    private InputInterface $input;

    private OutputInterface $output;

    /**
     * Style constructor.
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($this->input = $input, $this->output = $output);
    }

    /**
     * Waits for Enter key.
     */
    public function waitForKey(string $category): Style
    {
        $stdin = fopen('php://stdin', 'rb');

        if ($stdin !== false && $this->output instanceof ConsoleOutput && $this->input->isInteractive()) {
            $this->newLine();
            /** @var ConsoleSectionOutput $section */
            $section = $this->output->section();
            $section->writeln(sprintf('<title>Press enter to see %s issues...</title>', strtolower($category)));
            fgetc($stdin);
            $section->clear(3);
        }

        return $this;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }
}
