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
     * @var array<string>
     */
    private $errors;

    /**
     * @var array<string>
     */
    private $publishErrors;

    /**
     * @var array<string>
     */
    private $warnings;

    public function hasIssue(): bool
    {
        $validator = new ConfigValidator(new NullIO());
        [$this->errors, $this->publishErrors, $this->warnings] = $validator->validate(ComposerFinder::getPath($this->collector));

        return \count(\array_merge($this->errors, $this->publishErrors, $this->warnings)) > 0;
    }

    public function getTitle(): string
    {
        return 'Composer.json is not valid';
    }

    public function getDetails(): array
    {
        $details = [];

        foreach (array_merge($this->errors, $this->publishErrors, $this->warnings) as $issue) {
            if (strpos($issue, ' : ') !== false) {
                $issue = explode(' : ', $issue)[1];
            }
            $details[] = Details::make()
                ->setFile('composer.json')
                ->setMessage($issue);
        }

        return $details;
    }
}
