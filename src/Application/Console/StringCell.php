<?php

namespace NunoMaduro\PhpInsights\Application\Console;

/**
 * @internal
 */
final class StringCell
{
    /**
     * @var string
     */
    private $value;

    /**
     * Creates a new instance of string cell.
     *
     * @param  string  $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
