<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use Composer\Semver\Semver;

/**
 * @internal
 */
final class Composer
{
    /** @var array<string, string|int|array> */
    private $config;

    /**
     * Composer constructor.
     *
     * @param array<string, string|int|array> $data
     */
    public function __construct(array $data)
    {
        $this->config = $data;
    }

    public static function fromPath(string $path): self
    {
        return new self(json_decode((string) file_get_contents($path), true));
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

    public function getName(): ?string
    {
        return $this->config['name'] ?? null;
    }

    public function getPhpVersion(): ?string
    {
        return $this->getRequirements()['php'] ?? null;
    }

    public function lowestPhpVersionIsGreaterThenOrEqualTo(string $version): ?bool
    {
        $composerVersion = $this->getPhpVersion();

        if ($composerVersion === null) {
            return null;
        }
        $composerVersion = str_replace('^', '', $composerVersion);

        return Semver::satisfies($composerVersion, $version);
    }
}
