<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Definitions;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * @internal
 */
final class AnalyseDefinition extends BaseDefinition
{
    public static function get(): InputDefinition
    {
        $definition = parent::get();

        $definition->addOptions([
            new InputOption(
                'min-quality',
                null,
                InputOption::VALUE_OPTIONAL,
                'Minimal Quality level to reach without throw error',
                '0'
            ),
            new InputOption(
                'min-complexity',
                null,
                InputOption::VALUE_OPTIONAL,
                'Minimal Complexity level to reach without throw error',
                '0'
            ),
            new InputOption(
                'min-architecture',
                null,
                InputOption::VALUE_OPTIONAL,
                'Minimal Architecture level to reach without throw error',
                '0'
            ),
            new InputOption(
                'min-style',
                null,
                InputOption::VALUE_OPTIONAL,
                'Minimal Style level to reach without throw error',
                '0'
            ),
            new InputOption(
                'disable-security-check',
                null,
                InputOption::VALUE_NONE,
                'Disable Security issues check to not throw error if vulnerability is found'
            ),
            new InputOption(
                'format',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Format to output the result in [console, json, checkstyle, codeclimate]',
                ['console']
            ),
            new InputOption(
                'composer',
                null,
                InputOption::VALUE_OPTIONAL,
                'The composer file path'
            ),
            new InputOption(
                'fix',
                null,
                InputOption::VALUE_NONE,
                'Enable auto-fix for fixable insights'
            ),
            new InputOption(
                'flush-cache',
                null,
                InputOption::VALUE_NONE,
                'Flush cache results before processing'
            ),
        ]);

        return $definition;
    }
}
