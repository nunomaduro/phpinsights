<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InvokableCommand extends BaseCommand
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * Creates a new instance of the Invokable Command.
     *
     * @param string $name
     * @param callable $callable
     * @param InputDefinition $definition
     */
    public function __construct(string $name, callable $callable, InputDefinition $definition)
    {
        parent::__construct($name);

        $this->setDefinition($definition);

        $this->callable = $callable;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = call_user_func($this->callable, $input, $output);

        if ($output instanceof ConsoleOutputInterface) {
            $output->getErrorOutput()->writeln('✨ See something that needs to be improved? <options=bold>Create an issue</> or send us a <options=bold>pull request</>: <fg=cyan;options=bold>https://github.com/nunomaduro/phpinsights</>');
        }

        return $result;
    }
}
