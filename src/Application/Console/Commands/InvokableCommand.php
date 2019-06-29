<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
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
     * @param  string  $name
     * @param  callable  $callable
     * @param  array<int, \Symfony\Component\Console\Input\InputArgument|\Symfony\Component\Console\Input\InputOption>  $definition
     */
    public function __construct(string $name, callable $callable, array $definition)
    {
        parent::__construct($name);

        $this->setDefinition($definition);

        $this->callable = $callable;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        return call_user_func($this->callable, $input, $output);
    }
}
