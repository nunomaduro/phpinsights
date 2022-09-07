<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use SebastianBergmann\Diff\Differ as BaseDiffer;
use SebastianBergmann\Diff\Output\StrictUnifiedDiffOutputBuilder;
use PhpCsFixer\Differ\DifferInterface;

final class Differ implements DifferInterface
{
    private BaseDiffer $differ;

    public function __construct()
    {
        $diffContext = Container::make()->get(Configuration::class)->getDiffContext();

        $outputBuilder = new StrictUnifiedDiffOutputBuilder([
            'collapseRanges' => true,
            'commonLineThreshold' => 1,
            'contextLines' => $diffContext,
            'fromFile' => '',
            'toFile' => '',
        ]);

        $this->differ = new BaseDiffer($outputBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function diff(string $old, string $new, ?\SplFileInfo $file = null): string
    {
        return $this->differ->diff($old, $new);
    }
}
