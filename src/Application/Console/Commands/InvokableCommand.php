<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use function call_user_func;
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
     * @param  array<\Symfony\Component\Console\Input\InputArgument|\Symfony\Component\Console\Input\InputOption>  $definition
     */
    public function __construct(string $name, callable $callable, array $definition)
    {
        parent::__construct($name);

        $this->setDefinition($definition);

        $this->callable = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        call_user_func($this->callable, $input, $output);

        return 0;
    }
}
