<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants;
use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\NoSilencedErrorsSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Preset
    |--------------------------------------------------------------------------
    |
    | This option controls the default preset that will be used by PHP Insights
    | to make your code reliable, simple, and clean. However, you can always
    | adjust the `Metrics` and `Insights` below in this configuration file.
    |
    | Supported: "default", "laravel", "symfony", "magento2", "drupal"
    |
    */

    'preset' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may adjust all the various `Insights` that will be used by PHP
    | Insights. You can either add, remove or configure `Insights`. Keep in
    | mind, that all added `Insights` must belong to a specific `Metric`.
    |
    */

    'exclude' => [
    ],

    'add' => [
        \NunoMaduro\PhpInsights\Domain\Metrics\Code\Comments::class => [
            \PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer::class,
        ],
    ],

    'remove' => [
    ],

    'config' => [
        LineLengthSniff::class => [
            'lineLimit' => 80,
            'absoluteLineLimit' => 120,
            'ignoreComments' => true,
        ],
        DisallowMixedTypeHintSniff::class => [
            'exclude' => [
                'src/Domain/Reflection.php',
                'src/Domain/Details.php',
            ],
        ],
        ForbiddenSetterSniff::class => [
            'exclude' => [
                'src/Domain/Reflection.php',
                'src/Domain/Details.php',
            ],
        ],
        NoSilencedErrorsSniff::class => [
            'exclude' => [
                'src/Domain/Analyser.php',
                'src/Domain/File.php',
            ],
        ],
        ForbiddenDefineGlobalConstants::class => [
            'ignore' => [
                'PHP_CODESNIFFER_VERBOSITY',
                'PHP_CODESNIFFER_CBF',
            ],
        ],
        UnusedParameterSniff::class => [
            'exclude' => [
                'src/Domain/LinkFormatter/NullFileLinkFormatter.php',
            ],
        ],
        PropertyTypeHintSniff::class => [
            'enableNativeTypeHint' => false,
        ],
    ],
];
