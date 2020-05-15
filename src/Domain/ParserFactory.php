<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PhpParser\Lexer;
use PhpParser\Lexer\Emulative;
use PhpParser\Parser;
use PhpParser\ParserFactory as NikicParserFactory;

final class ParserFactory
{
    private static Lexer $lexer;

    public static function createParser(): Parser
    {
        return (new NikicParserFactory())
            ->create(NikicParserFactory::PREFER_PHP7, self::getLexer(), [
                'useIdentifierNodes' => true,
                'useConsistentVariableNodes' => true,
                'useExpressionStatements' => true,
                'useNopStatements' => false,
            ]);
    }

    public static function getLexer(): Lexer
    {
        return self::$lexer ??= self::createLexer();
    }

    private static function createLexer(): Emulative
    {
        return new Emulative([
            'usedAttributes' => ['comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos'],
        ]);
    }
}
