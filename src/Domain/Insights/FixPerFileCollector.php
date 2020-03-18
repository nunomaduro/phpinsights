<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Details;

/**
 * @internal
 */
trait FixPerFileCollector
{
    /**
     * @var int
     */
    private $totalFixed = 0;

    /**
     * @var array<string, int>
     */
    private $fixPerFile = [];

    public function addFileFixed(string $file): void
    {
        if (! \array_key_exists($file, $this->fixPerFile)) {
            $this->fixPerFile[$file] = 0;
        }

        $this->fixPerFile[$file]++;
        $this->totalFixed++;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixPerFile(): array
    {
        $details = [];
        foreach ($this->fixPerFile as $file => $count) {
            $message = 'issues fixed';
            if ($count === 1) {
                $message = 'issue fixed';
            }

            $details[] = (new Details())
                ->setMessage(sprintf('%s %s', $count, $message))
                ->setFile($file);
        }

        return $details;
    }

    public function getTotalFix(): int
    {
        return $this->totalFixed;
    }
}
