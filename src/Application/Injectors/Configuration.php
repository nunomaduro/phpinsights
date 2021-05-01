<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Commands\InternalProcessorCommand;
use NunoMaduro\PhpInsights\Application\Console\Definitions\DefinitionBinder;
use NunoMaduro\PhpInsights\Domain\Configuration as DomainConfiguration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidConfiguration;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @internal
 *
 * @see \Tests\Domain\ConfigurationTest
 */
final class Configuration
{
    /**
     * Inject Configuration resolved into the container definitions.
     *
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            DomainConfiguration::class => static function (): DomainConfiguration {
                $input = new ArgvInput();
                if (
                    $input->getFirstArgument() === InternalProcessorCommand::NAME &&
                    Container::make()->get(CacheInterface::class)->has('current_configuration')
                ) {
                    // Use cache only for internal:processor, not other commands
                    return Container::make()->get(CacheInterface::class)->get('current_configuration');
                }

                DefinitionBinder::bind($input);
                $configPath = ConfigResolver::resolvePath($input);
                $config = [];

                if ($configPath !== '' && file_exists($configPath)) {
                    $config = require $configPath;
                }

                /**
                 * @noRector Rector\CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector
                 */
                $fixOption = $input->hasOption('fix') && (bool) $input->getOption('fix') === true;

                $config['fix'] = $fixOption || $input->getFirstArgument() === 'fix';

                try {
                    return ConfigResolver::resolve($config, $input);
                } catch (InvalidConfiguration $exception) {
                    (new ConsoleOutput())->getErrorOutput()
                        ->writeln([
                            '',
                            '  <bg=red;options=bold> Invalid configuration </>',
                            '    <fg=red>•</> <options=bold>' . $exception->getMessage() . '</>',
                            '',
                        ]);
                    die(1);
                }
            },
        ];
    }
}
