<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Composer;

use Composer\IO\NullIO;
use Composer\Util\ConfigValidator;
use NunoMaduro\PhpInsights\Domain\ComposerFinder;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;

final class ComposerMustBeValid extends Insight implements HasDetails
{
    /**
     * @var bool
     */
    private $analyzed = false;

    /**
     * @var array<Details>
     */
    private $details = [];

    public function hasIssue(): bool
    {
        if (! $this->analyzed) {
            $this->process();
        }

        return count($this->details) > 0;
    }

    public function getTitle(): string
    {
        return 'Composer.json is not valid';
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    private function process(): void
    {
        $validator = new ConfigValidator(new NullIO());

        [$errors, $publishErrors, $warnings] = $validator->validate(ComposerFinder::getPath($this->collector));

        foreach (array_merge($errors, $publishErrors, $warnings) as $issue) {
            if (strpos($issue, ' : ') !== false) {
                $issue = explode(' : ', $issue)[1];
            }
            $this->details[] = Details::make()
                ->setFile('composer.json')
                ->setMessage($issue);
        }

        $this->analyzed = true;
    }
}
