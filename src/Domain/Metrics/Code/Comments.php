<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Code;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\FixmeSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff;
use PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer;
use PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer;
use SlevomatCodingStandard\Sniffs\Commenting\EmptyCommentSniff;
use SlevomatCodingStandard\Sniffs\Commenting\ForbiddenCommentsSniff;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
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
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getCommentLines());
    }

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
            // FullyQualifiedClassNameInAnnotationSniff::class,
            NullableTypeForNullDefaultValueSniff::class,
            FixmeSniff::class,
            TodoSniff::class,
            ForbiddenCommentsSniff::class,
            InlineDocCommentDeclarationSniff::class,
            DisallowArrayTypeHintSyntaxSniff::class,
            DisallowMixedTypeHintSniff::class,
            LongTypeHintsSniff::class,
            NullTypeHintOnLastPositionSniff::class,
            TypeHintDeclarationSniff::class,
            UselessConstantTypeHintSniff::class,
            UselessInheritDocCommentSniff::class,
            NoBreakCommentFixer::class,
            MultilineCommentOpeningClosingFixer::class,
            NoEmptyCommentFixer::class,
            PhpdocScalarFixer::class,
        ];
    }
}
