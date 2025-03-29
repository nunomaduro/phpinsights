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

    public static function get(Composer $composer): array
    {
        return [
            'exclude' => [
                'bower_components',
                'node_modules',
                'vendor',
                'vendor-bin',
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
                    'linesCountBeforeDeclare' => 1,
                    'spacesCountAroundEqualsSign' => 0,
                ],
                UnusedUsesSniff::class => [
                    'searchAnnotations' => true,
                ],
                UnusedVariableSniff::class => [
                    'ignoreUnusedValuesWhenOnlyKeysAreUsedInForeach' => true,
                ],
                PropertyTypeHintSniff::class => [
                    'enableNativeTypeHint' => true,
                ],
            ],
        ];
    }

    public static function shouldBeApplied(Composer $composer): bool
    {
        return true;
    }
}
