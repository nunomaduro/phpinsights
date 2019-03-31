<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use ReflectionClass;
use SebastianBergmann\PHPLOC\Analyser as BaseAnalyser;

/**
 * @internal
 */
final class Analyser extends BaseAnalyser
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Collector
     */
    private $collector;

    /**
     * Creates a new instance of the Analyser.
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        parent::__construct();

        $reflectionClass = new ReflectionClass(BaseAnalyser::class);

        $reflectionProperty = $reflectionClass->getProperty('collector');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this, $this->collector = new Collector());
    }

    /**
     * @return \NunoMaduro\PhpInsights\Domain\Collector
     */
    public function getCollector(): Collector
    {
        return $this->collector;
    }
}