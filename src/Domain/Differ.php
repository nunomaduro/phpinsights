<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PhpCsFixer\Diff\v3_0\Differ as BaseDiffer;
use PhpCsFixer\Diff\v3_0\Output\DiffOnlyOutputBuilder;
use PhpCsFixer\Differ\DifferInterface;

final class Differ implements DifferInterface
{

    /**
     * @var \PhpCsFixer\Diff\v3_0\Differ
     */
    private $differ;

    public function __construct()
    {
        $outputBuilder = new DiffOnlyOutputBuilder('');
        $this->differ = new BaseDiffer($outputBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function diff($old, $new): string
    {
        return $this->differ->diff($old, $new);
    }
}
