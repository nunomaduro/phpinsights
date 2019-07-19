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

    /** @var int */
    private $severity = 5;

    public static function make(): Details
    {
        return new self();
    }

    public function setFile(string $file): Details
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @param mixed $original
     *
     * @return Details
     */
    public function setOriginal($original): Details
    {
        $this->original = $original;
        return $this;
    }

    public function setLine(int $line): Details
    {
        $this->line = $line;
        return $this;
    }

    public function setMessage(string $message): Details
    {
        $this->message = $message;
        return $this;
    }

    public function setFunction(string $function): Details
    {
        $this->function = $function;
        return $this;
    }

    public function setSeverity(int $severity): Details
    {
        $this->severity = $severity;
        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function hasFile(): bool
    {
        return $this->file !== null;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function hasLine(): bool
    {
        return $this->line !== null;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function hasMessage(): bool
    {
        return $this->message !== null;
    }

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

    public function getSeverity(): int
    {
        return $this->severity;
    }
}
