<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @internal
 */
final class IOProvider extends AbstractServiceProvider
{
    /** @var array<class-string> */
    protected $provides = [
        InputInterface::class
    ];

    public function register()
    {
        $this->getLeagueContainer()->add(
            InputInterface::class,
            function () {
                $input = new ArgvInput();
                // merge application default definition with analyse definition.
                $definition = (new Application())->getDefinition();
                $analyseDefinition = AnalyseDefinition::get();

                $definition->addArguments($analyseDefinition->getArguments());
                $definition->addOptions($analyseDefinition->getOptions());

                $input->bind($definition);
                return $input;
            }
        );
    }
}
