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
    /** @var array<int, string> */
    const FUNDING_MESSAGES = [
        '',
        '  - Star or contribute to PHP Insights:',
        '    <options=bold>https://github.com/nunomaduro/phpinsights</>',
        '  - Sponsor the maintainers:',
        '    <options=bold>https://github.com/sponsors/nunomaduro</>',
    ];

    /**
     * @var callable
     */
    private $callable;

    /**
     * Creates a new instance of the Invokable Command.
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
            foreach (self::FUNDING_MESSAGES as $message) {
                $output->getErrorOutput()->writeln($message);
            }
        }

        return $result;
    }
}
