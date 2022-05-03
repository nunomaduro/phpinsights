<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

/**
 * @internal
 */
final class Composer
{
    /** @var array<string, mixed> */
    private array $config;

    /**
     * Composer constructor.
     *
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->config = $data;
    }

    public static function fromPath(string $path): self
    {
        return new self(json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @return array<string, string>
     */
    public function getRequirements(): array
    {
        return $this->config['require'] ?? [];
    }

    /**
     * @return array<string, string>
     */
    public function getReplacements(): array
    {
        return $this->config['replace'] ?? [];
    }

    public function getName(): string
    {
        return $this->config['name'] ?? '';
    }
}
