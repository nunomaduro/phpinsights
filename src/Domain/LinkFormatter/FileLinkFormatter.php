<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\LinkFormatter;

use NunoMaduro\PhpInsights\Domain\Contracts\FileLinkFormatter as FileLinkFormatterContract;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidConfiguration;

/**
 * @internal
 */
final class FileLinkFormatter implements FileLinkFormatterContract
{
    /**
     * @var string
     */
    private $pattern;

    public function __construct(string $pattern)
    {
        if (mb_strpos($pattern, '%f') === false ||
            mb_strpos($pattern, '%l') === false
        ) {
            throw new InvalidConfiguration('Unparsable pattern "'.$pattern.'" to handle hyperlinks');
        }

        $this->pattern = $pattern;
    }

    public function format(string $file, int $line): string
    {
        return strtr($this->pattern, ['%f' => $file, '%l' => $line]);
    }
}
