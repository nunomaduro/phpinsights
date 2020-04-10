<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use Composer\Semver\Semver;

/**
 * @internal
 *
 * @see \Tests\Application\ComposerTest
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

    public function getPhpVersion(): string
    {
        return $this->getRequirements()['php'];
    }

    public function hasPhpVersion(): bool
    {
        return isset($this->getRequirements()['php']);
    }

    public function lowestPhpVersionIsGreaterThenOrEqualTo(string $version): bool
    {
        $composerVersion = $this->getPhpVersion();
        preg_match("/\d+(\.\d+)/", $composerVersion, $matches);
        $composerVersion = $matches[0];

        return Semver::satisfies($composerVersion, $version);
    }
}
