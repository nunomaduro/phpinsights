<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\GlobalInsight;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Exceptions\InternetConnectionNotFound;
use SensioLabs\Security\Result;
use SensioLabs\Security\SecurityChecker;
use Throwable;

final class ForbiddenSecurityIssues extends Insight implements HasDetails, GlobalInsight
{
    private static ?Result $result = null;

    /**
     * @var array<Details>
     */
    private static ?array $details = null;

    public function hasIssue(): bool
    {
        return (bool) count($this->getDetails());
    }

    public function getTitle(): string
    {
        return 'Security issues found on dependencies';
    }

    public function process(): void
    {
        $this->getResult();
    }

    public function getDetails(): array
    {
        if (self::$details !== null) {
            return self::$details;
        }

        try {
            $issues = json_decode((string) $this->getResult(), true, 512, JSON_THROW_ON_ERROR);
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
                    '%scomposer.lock',
                    $this->collector->getCommonPath()
                ));
            } catch (Throwable $e) {
                throw new InternetConnectionNotFound(
                    'PHP Insights needs an internet connection to inspect security issues.',
                    1,
                    $e
                );
            }
        }

        return self::$result;
    }
}
