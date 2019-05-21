<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff;

/**
 * @internal
 */
final class DefaultPreset implements PresetContract
{
    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'default';
    }

    /**
     * {@inheritdoc}
     */
    public static function get(): array
    {
        return [
            'exclude' => [
                // ...
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
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function shouldBeApplied(array $composer): bool
    {
        return true;
    }
}
