<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;

/**
 * @internal
 */
final class DefaultPreset implements PresetContract
{
    public static function getName(): string
    {
        return 'default';
    }

    public static function get(?Composer $composer): array
    {
        $config = [
            'exclude' => [
                'bower_components',
                'node_modules',
                'vendor',
                '.phpstorm.meta.php',
            ],
            'add' => [
                // ...
            ],
            'remove' => [
                // ...
            ],
            'config' => [
                DocCommentSpacingSniff::class => [
                    'linesCountBetweenDifferentAnnotationsTypes' => 1,
                ],
                DeclareStrictTypesSniff::class => [
                    'newlinesCountBetweenOpenTagAndDeclare' => 2,
                    'spacesCountAroundEqualsSign' => 0,
                ],
                UnusedUsesSniff::class => [
                    'searchAnnotations' => true,
                ],
                UnusedVariableSniff::class => [
                    'ignoreUnusedValuesWhenOnlyKeysAreUsedInForeach' => true,
                ],
                PropertyTypeHintSniff::class => [
                    'enableNativeTypeHint' => $composer !== null ? $composer->lowestPhpVersionIsGreaterThenOrEqualTo('7.4') === true : null,
                ]
            ],
        ];

        return array_filter($config);
    }

    public static function shouldBeApplied(Composer $composer): bool
    {
        return true;
    }
}
