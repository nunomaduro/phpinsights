<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Definitions\DefinitionBinder;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * @internal
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
            \NunoMaduro\PhpInsights\Domain\Configuration::class => static function (): \NunoMaduro\PhpInsights\Domain\Configuration {
                $input = new ArgvInput();

                DefinitionBinder::bind($input);
                $configPath = ConfigResolver::resolvePath($input);
                $config = [];

                if ($configPath !== '' && file_exists($configPath)) {
                    $config = require $configPath;
                }

                $fixOption = $input->hasOption('fix') && (bool) $input->getOption('fix') === true;

                $config['fix'] = $fixOption || $input->getFirstArgument() === 'fix';

                return ConfigResolver::resolve($config, $input);
            },
        ];
    }
}
