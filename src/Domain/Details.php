<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

final class Details
{
    /** @var mixed */
    private $original;

    /** @var string */
    private $file;

    /** @var int */
    private $line;

    /** @var string  */
    private $function;

    /** @var string */
    private $message;

    public static function make(): Details
    {
        return new Details();
    }

    /**
     * @param string $file
     *
     * @return Details
     */
    public function withFile(string $file): Details
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @param mixed $original
     *
     * @return Details
     */
    public function withOriginal($original): Details
    {
        $this->original = $original;
        return $this;
    }

    /**
     * @param int $line
     *
     * @return Details
     */
    public function withLine(int $line): Details
    {
        $this->line = $line;
        return $this;
    }

    /**
     * @param string $message
     *
     * @return Details
     */
    public function withMessage(string $message): Details
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param string $function
     *
     * @return Details
     */
    public function withFunction(string $function): Details
    {
        $this->function = $function;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    public function hasFile(): bool
    {
        return $this->file !== null;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    public function hasLine(): bool
    {
        return $this->line !== null;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function hasMessage(): bool
    {
        return $this->message !== null;
    }

    /**
     * @return string
     */
    public function getFunction(): string
    {
        return $this->function;
    }

    public function hasFunction(): bool
    {
        return $this->function !== null;
    }

    /**
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->original;
    }

    public function hasOriginal(): bool
    {
        return $this->original !== null;
    }
}
