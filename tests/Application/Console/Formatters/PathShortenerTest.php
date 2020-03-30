<?php

declare(strict_types=1);

namespace Tests\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use Tests\TestCase;

final class PathShortenerTest extends TestCase
{
    /**
     * @return array<string, array<string[]|string>>
     */
    public static function commonPathExtractProvider(): array
    {
        return [
            'Shorten same directory' => [
                [
                    '/Users/MyUser/Code/phpinsights/src/Application/ConfigResolver.php',
                    '/Users/MyUser/Code/phpinsights/src/Application/Composer.php',
                ],
                '/Users/MyUser/Code/phpinsights/src/Application/',
            ],
            'Shorten same sub directory' => [
                [
                    '/Users/MyUser/Code/phpinsights/src/Domain/ComposerFinder.php',
                    '/Users/MyUser/Code/phpinsights/src/Application/ConfigResolver.php',
                    '/Users/MyUser/Code/phpinsights/src/Application/Composer.php',
                ],
                '/Users/MyUser/Code/phpinsights/src/',
            ]
        ];
    }

    /**
     * @param array<string> $paths
     *
     * @dataProvider commonPathExtractProvider
     */
    public function testExtractingCommonPath(array $paths, string $commonPath): void
    {
        self::assertEquals(
            $commonPath,
            PathShortener::extractCommonPath($paths)
        );
    }

}
