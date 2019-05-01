<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Code;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\FixmeSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DisallowOneLinePropertyDocCommentSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;
use SlevomatCodingStandard\Sniffs\Commenting\EmptyCommentSniff;
use SlevomatCodingStandard\Sniffs\Commenting\ForbiddenCommentsSniff;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Commenting\RequireOneLinePropertyDocCommentSniff;
use SlevomatCodingStandard\Sniffs\Commenting\UselessInheritDocCommentSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\LongTypeHintsSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\NullableTypeForNullDefaultValueSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\NullTypeHintOnLastPositionSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\UselessConstantTypeHintSniff;

final class Comments implements HasValue, HasPercentage, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getCommentLines());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Collector $collector): float
    {
        return $collector->getLines() > 0 ? ($collector->getCommentLines() / $collector->getLines()) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            EmptyCommentSniff::class,
            TypeHintDeclarationSniff::class,
            NullTypeHintOnLastPositionSniff::class,
            NullableTypeForNullDefaultValueSniff::class,
            FixmeSniff::class,
            TodoSniff::class,
            DisallowOneLinePropertyDocCommentSniff::class,
            DocCommentSpacingSniff::class,
            ForbiddenCommentsSniff::class,
            InlineDocCommentDeclarationSniff::class,
            RequireOneLinePropertyDocCommentSniff::class,
            UselessInheritDocCommentSniff::class,
            DisallowArrayTypeHintSyntaxSniff::class,
            DisallowMixedTypeHintSniff::class,
            LongTypeHintsSniff::class,
            NullTypeHintOnLastPositionSniff::class,
            NullableTypeForNullDefaultValueSniff::class,
            TypeHintDeclarationSniff::class,
            UselessConstantTypeHintSniff::class,
        ];
    }
}
