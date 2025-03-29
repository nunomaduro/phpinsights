<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

final class Details
{
    private mixed $original;

    private ?string $file = null;

    private ?int $line = null;

    private ?string $function = null;

    private ?string $message = null;

    private ?string $diff = null;

    public static function make(): Details
    {
        return new self();
    }

    public function setFile(string $file): Details
    {
        $this->file = $file;

        return $this;
    }

    public function setOriginal(mixed $original): Details
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

    public function setDiff(string $diff): Details
    {
        $this->diff = $diff;

        return $this;
    }

    public function getFile(): string
    {
        return $this->file ?? '';
    }

    public function hasFile(): bool
    {
        return $this->file !== null;
    }

    public function getLine(): int
    {
        return $this->line ?? 0;
    }

    public function hasLine(): bool
    {
        return $this->line !== null;
    }

    public function getMessage(): string
    {
        return $this->message ?? '';
    }

    public function hasMessage(): bool
    {
        return $this->message !== null;
    }

    public function getFunction(): string
    {
        return $this->function ?? '';
    }

    public function hasFunction(): bool
    {
        return $this->function !== null;
    }

    public function getOriginal(): mixed
    {
        return $this->original;
    }

    public function hasOriginal(): bool
    {
        return $this->original !== null;
    }

    public function getDiff(): string
    {
        return $this->diff ?? '';
    }

    public function hasDiff(): bool
    {
        return $this->diff !== null;
    }
}
