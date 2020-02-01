<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Exceptions\InternetConnectionNotFound;
use SensioLabs\Security\Result;
use SensioLabs\Security\SecurityChecker;

final class ForbiddenSecurityIssues extends Insight implements HasDetails
{
    /**
     * @var \SensioLabs\Security\Result|null
     */
    private static $result;

    /**
     * @var array<Details>
     */
    private static $details;

    public function hasIssue(): bool
    {
        return (bool) count($this->getDetails());
    }

    public function getTitle(): string
    {
        return 'Security issues found on dependencies';
    }

    public function getDetails(): array
    {
        if (self::$details !== null) {
            return self::$details;
        }

        try {
            $issues = json_decode((string) $this->getResult(), true);
        } catch (InternetConnectionNotFound $exception) {
            self::$details = [
                Details::make()->setMessage($exception->getMessage()),
            ];

            return self::$details;
        }

        if ($issues === null) {
            return [];
        }

        self::$details = [];
        foreach ($issues as $packageName => $package) {
            foreach ($package['advisories'] as $advisory) {
                self::$details[] = Details::make()->setMessage(
                    "${packageName}@{$package['version']} {$advisory['title']} - {$advisory['link']}"
                );
            }
        }

        return self::$details;
    }

    private function getResult(): Result
    {
        if (self::$result === null) {
            $checker = new SecurityChecker();

            try {
                self::$result = $checker->check(sprintf(
                    '%s/composer.lock', $this->collector->getDir()
                ));
            } catch (\Throwable $e) {
                throw new InternetConnectionNotFound('PHP Insights needs an internet connection to inspect security issues.', 1, $e);
            }
        }

        return self::$result;
    }
}
