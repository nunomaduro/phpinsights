<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use Composer\Semver\Semver;
use NunoMaduro\PhpInsights\Domain\Contracts\GlobalInsight;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use NunoMaduro\PhpInsights\Domain\Exceptions\InternetConnectionNotFound;
use Symfony\Component\HttpClient\HttpClient;
use Throwable;

final class ForbiddenSecurityIssues extends Insight implements HasDetails, GlobalInsight
{
    private const PACKAGIST_ADVISORIES_URL = 'https://repo.packagist.org/api/security-advisories/';

    /**
     * @var array<Details>
     */
    private static ?array $details = null;

    public function __destruct()
    {
        self::$details = null;
    }

    public function hasIssue(): bool
    {
        return (bool) \count($this->getDetails());
    }

    public function getTitle(): string
    {
        return 'Security issues found on dependencies';
    }

    public function process(): void
    {
        $this->getDetails();
    }

    public function getDetails(): array
    {
        if (self::$details !== null) {
            return self::$details;
        }

        try {
            $this->getResult();
        } catch (InternetConnectionNotFound $exception) {
            self::$details = [
                Details::make()->setMessage($exception->getMessage()),
            ];

            return self::$details;
        }

        return self::$details;
    }

    private function getResult(): void
    {
        $composerPath = sprintf('%scomposer.lock', $this->collector->getCommonPath());

        if (! file_exists($composerPath)) {
            throw new ComposerNotFound('composer.lock not found. Try launch composer install');
        }

        $packages = json_decode(file_get_contents($composerPath), true);
        $packagesToCheck = array_combine(
            array_map(static function (array $detail): string {
                return $detail['name'];
            }, $packages['packages']),
            array_map(static function (array $detail): string {
                return $detail['version'];
            }, $packages['packages'])
        );

        try {
            $advisoryList = $this->retrieveAdvisoriesListForPackages(array_keys($packagesToCheck));
        } catch (Throwable $e) {
            throw new InternetConnectionNotFound(
                'PHP Insights needs an internet connection to inspect security issues.',
                1,
                $e
            );
        }

        self::$details = [];
        $packagesToCheck = array_filter(
            $packagesToCheck,
            static function (string $packageName) use ($advisoryList): bool {
                return \array_key_exists($packageName, $advisoryList['advisories']);
            },
            ARRAY_FILTER_USE_KEY
        );

        foreach ($packagesToCheck as $packageName => $version) {
            $issues = $advisoryList['advisories'][$packageName];

            $this->addIssuesDetails($packageName, $version, $issues);
        }
    }

    private function retrieveAdvisoriesListForPackages(array $packagesName): array
    {
        $client = HttpClient::create();

        $chunks = array_chunk($packagesName, 30);
        $responses = [];
        // Launch async requests
        foreach ($chunks as $packages) {
            $responses[] = $client->request('GET', self::PACKAGIST_ADVISORIES_URL, [
                'query' => ['packages' => $packages],
            ]);
        }

        $advisories = [];
        /** @var \Symfony\Contracts\HttpClient\ResponseInterface $response */
        foreach ($responses as $response) {
            $advisories[] = $response->toArray()['advisories'];
        }
        $allAdvisories = array_merge([], ...$advisories);

        return ['advisories' => $allAdvisories];
    }

    private function addIssuesDetails(string $packageName, string $version, array $issues): void
    {
        foreach ($issues as $issue) {
            if (! Semver::satisfies($version, $issue['affectedVersions'])) {
                continue;
            }
            self::$details[] = Details::make()->setMessage(
                "${packageName}@{$version} {$issue['title']} - {$issue['link']}"
            );
        }
    }
}
