<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
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
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (bool)count($this->getDetails());
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'Security issues found on dependencies';
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        $issues = json_decode((string)$this->getResult(), true);

        if ($issues === null) {
            return [];
        }

        $details = [];

        foreach ($issues as $packageName => $package) {
            foreach ($package['advisories'] as $advisory) {
                $details[] = "$packageName@{$package['version']} {$advisory['title']} - {$advisory['link']}";
            }
        }

        return $details;
    }

    /**
     * @return \SensioLabs\Security\Result
     */
    private function getResult(): Result
    {
        if (self::$result === null) {
            $checker = new SecurityChecker();

            try {
                self::$result = $checker->check(
                    sprintf(
                        '%s/composer.lock',
                        $this->collector->getDir()
                    )
                );
            } catch (\Throwable $e) {
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
