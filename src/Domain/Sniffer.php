<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Sniffers\ForbiddenFunctions;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;

/**
 * @internal
 */
final class Sniffer
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Collector
     */
    private $collector;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Config
     */
    private $config;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Ruleset
     */
    private $ruleset;

    /**
     * @var string[]
     */
    private $sniffers = [
        ForbiddenFunctions::class,
    ];

    /**
     * @var array<string,
     */
    private $collectors;

    public function __construct(Collector $collector, array $sniffs = [])
    {
        $this->config = new Config([getcwd()]);
        $this->ruleset = new Ruleset($this->config);
        $this->ruleset->sniffs = [];
        foreach ($this->sniffers as $snifferClass) {
            $sniff = ($sniffer = new $snifferClass)->getSniff();
            $this->ruleset->sniffs[get_class($sniff)] = $sniff;
            $this->collectors[get_class($sniff)] = $sniffer;
        }

        $this->ruleset->populateTokenListeners();

        $this->collector = $collector;
    }

    /**
     * Sniffs the given filename.
     *
     * @param  string  $filename
     */
    public function sniff($filename): void
    {
        $file = new File($filename, $this->ruleset, $this->config);
        $file->setContent(file_get_contents($filename));

        $file->process();
        foreach ($file->getErrors() as $line => $a) {
            foreach ($a as $b) {
                foreach ($b as $error) {
                    $error['line'] = $line;
                    $this->collectors[$error['listener']]->collect($this->collector, $error);
                }
            }
        }
    }
}
