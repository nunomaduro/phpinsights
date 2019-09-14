<?php


namespace NunoMaduro\PhpInsights\Domain\LinkFormatter;


use NunoMaduro\PhpInsights\Domain\Contracts\FileLinkFormatter as FileLinkFormatterContract;

class FileLinkFormatter implements FileLinkFormatterContract
{
    /**
     * @var string
     */
    private $pattern;

    public function __construct(string $pattern)
    {
        if (false === mb_strpos($pattern, '%f') ||
            false === mb_strpos($pattern, '%l')
        ) {
            throw new \RuntimeException('Unparsable pattern ' . $pattern);
        }

        $this->pattern = $pattern;
    }

    public function format(string $file, ?int $line): string
    {
        return strtr($this->pattern, ['%f' => $file, '%l' => $line ?? 0]);
    }
}
