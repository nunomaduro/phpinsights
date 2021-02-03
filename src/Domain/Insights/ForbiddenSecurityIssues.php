<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use Enlightn\SecurityChecker\SecurityChecker;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use NunoMaduro\PhpInsights\Domain\Exceptions\InternetConnectionNotFound;
use Throwable;

final class ForbiddenSecurityIssues extends Insight implements HasDetails
{
    /**
     * @var array
     */
    private static $result;

    /**
     * @var array<Details>
     */
    private static $details;

    public function hasIssue(): bool
    {
        return (bool) \count($this->getDetails());
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
            $issues = $this->getResult();
        } catch (InternetConnectionNotFound $exception) {
            self::$details = [
                Details::make()->setMessage($exception->getMessage()),
            ];
            return self::$details;
        }

        if (empty($issues)) {
            return [];
        }

        self::$details = [];
        foreach ($issues as $packageName => $package) {
            foreach ($package['advisories'] as $advisory) {
                self::$details[] = Details::make()->setMessage(
                    "${packageName}@v{$package['version']} {$advisory['title']} - {$advisory['link']}"
                );
            }
        }

        return self::$details;
    }

    private function getResult(): array
    {
        $composerPath = sprintf('%s/composer.lock', $this->collector->getDir());

        if (! file_exists($composerPath)) {
            throw new ComposerNotFound('composer.lock not found. Try launch composer install');
        }

        try {
            self::$result = (new SecurityChecker)->check($composerPath);
        } catch (Throwable $e) {
            throw new InternetConnectionNotFound(
                'PHP Insights needs an internet connection to inspect security issues.',
                1,
                $e
            );
        }

        return self::$result;
    }
}
