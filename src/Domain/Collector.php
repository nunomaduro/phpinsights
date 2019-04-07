<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use function count;
use function max;
use SplFileInfo;

/**
 * @internal
 */
final class Collector
{
    /**
     * @var int
     */
    private $lines = 0;

    /**
     * @var int
     */
    private $commentLines = 0;

    /**
     * @var int
     */
    private $logicalLines = 0;

    /**
     * @var int
     */
    private $functionLines = 0;

    /**
     * @var string[]
     */
    private $files = [];

    /**
     * @var string[]
     */
    private $directories = [];

    /**
     * @var string[]
     */
    private $concreteNonFinalClasses = [];

    /**
     * @var string[]
     */
    private $concreteFinalClasses = [];

    /**
     * @var int
     */
    private $abstractClasses = 0;

    /**
     * @var string[]
     */
    private $traits = [];

    /**
     * @var string[]
     */
    private $globalConstants = [];

    /**
     * @var int
     */
    private $interfaces = 0;

    /**
     * @var string[]
     */
    private $namespaces = [];

    /**
     * @var int
     */
    private $complexity = 0;

    /**
     * @var int
     */
    private $totalMethodComplexity = 0;

    /**
     * @var int[]
     */
    private $methodComplexity = [];

    /**
     * @var array<string, float>
     */
    private $classComplexity = [];

    /**
     * @var int
     */
    private $classConstants = 0;

    /**
     * @var int[]
     */
    private $methodLines = [];

    /**
     * @var array<string, float>
     */
    private $classLines = [];

    /**
     * @var int
     */
    private $staticAttributeAccesses = 0;

    /**
     * @var int
     */
    private $superGlobalVariableAccesses = 0;

    /**
     * @var string[]
     */
    private $possibleConstantAccesses = [];

    /**
     * @var int
     */
    private $globalVariableAccesses = 0;

    /**
     * @var int
     */
    private $nonStaticMethodCalls = 0;

    /**
     * @var int
     */
    private $nonStaticAttributeAccesses = 0;

    /**
     * @var int
     */
    private $anonymousFunctions = 0;

    /**
     * @var int
     */
    private $namedFunctions = 0;

    /**
     * @var int
     */
    private $publicMethods = 0;

    /**
     * @var int
     */
    private $staticMethods = 0;

    /**
     * @var int
     */
    private $nonStaticMethods = 0;

    /**
     * @var int
     */
    private $protectedMethods = 0;

    /**
     * @var int
     */
    private $privateMethods = 0;

    /**
     * @var int
     */
    private $staticMethodCalls = 0;


    /**
     * @var string
     */
    private $currentFilename = '';

    /**
     * @var int
     */
    private $currentClassComplexity = 0;

    /**
     * @var int
     */
    private $currentClassLines = 0;

    /**
     * @var int
     */
    private $currentMethodComplexity = 0;

    /**
     * @var int
     */
    private $currentMethodLines = 0;

    /**
     * @param  string  $filename
     *
     * @return void
     */
    public function addFile(string $filename): void
    {
        $this->files[] = $filename;
        $this->directories[] = \dirname($filename);
        $this->directories = array_unique($this->directories);

        $this->currentFilename = $filename;
    }

    /**
     * @param  int  $number
     *
     * @return void
     */
    public function incrementLines(int $number): void
    {
        $this->lines += $number;
    }

    /**
     * @param  int  $number
     *
     * @return void
     */
    public function incrementCommentLines(int $number): void
    {
        $this->commentLines += $number;
    }

    /**
     * @return void
     */
    public function incrementLogicalLines(): void
    {
        $this->logicalLines++;
    }

    /**
     * @return void
     */
    public function currentClassReset(): void
    {
        if ($this->currentClassComplexity > 0) {
            $this->classComplexity[$this->currentFilename] = $this->currentClassComplexity;
            $this->classLines[$this->currentFilename] = $this->currentClassLines;
        }

        $this->currentClassComplexity = 0;
        $this->currentClassLines = 0;
    }

    /**
     * @return void
     */
    public function currentClassIncrementComplexity(): void
    {
        $this->currentClassComplexity++;
    }

    /**
     * @return void
     */
    public function currentClassIncrementLines(): void
    {
        $this->currentClassLines++;
    }

    /**
     * @return void
     */
    public function currentMethodStart(): void
    {
        $this->currentMethodComplexity = 1;
        $this->currentMethodLines = 0;
    }

    /**
     * @return void
     */
    public function currentMethodIncrementComplexity(): void
    {
        $this->currentMethodComplexity++;
        $this->totalMethodComplexity++;
    }

    /**
     * @return void
     */
    public function currentMethodIncrementLines(): void
    {
        $this->currentMethodLines++;
    }

    /**
     * @return void
     */
    public function currentMethodStop(): void
    {
        $this->methodComplexity[] = $this->currentMethodComplexity;
        $this->methodLines[] = $this->currentMethodLines;
    }

    /**
     * @return void
     */
    public function incrementFunctionLines(): void
    {
        $this->functionLines++;
    }

    /**
     * @return void
     */
    public function incrementComplexity(): void
    {
        $this->complexity++;
    }

    /**
     * @param  string  $name
     *
     * @return void
     */
    public function addPossibleConstantAccesses(string $name): void
    {
        $this->possibleConstantAccesses[] = $name;
    }

    /**
     * @return void
     */
    public function incrementGlobalVariableAccesses(): void
    {
        $this->globalVariableAccesses++;
    }

    /**
     * @return void
     */
    public function incrementSuperGlobalVariableAccesses(): void
    {
        $this->superGlobalVariableAccesses++;
    }

    /**
     * @return void
     */
    public function incrementNonStaticAttributeAccesses(): void
    {
        $this->nonStaticAttributeAccesses++;
    }

    /**
     * @return void
     */
    public function incrementStaticAttributeAccesses(): void
    {
        $this->staticAttributeAccesses++;
    }

    /**
     * @return void
     */
    public function incrementNonStaticMethodCalls(): void
    {
        $this->nonStaticMethodCalls++;
    }

    /**
     * @return void
     */
    public function incrementStaticMethodCalls(): void
    {
        $this->staticMethodCalls++;
    }

    /**
     * @param  string  $namespace
     */
    public function addNamespace(string $namespace): void
    {
        $this->namespaces[] = $namespace;
        $this->namespaces = array_flip(array_flip($this->namespaces));

    }

    /**
     * @return void
     */
    public function incrementInterfaces(): void
    {
        $this->interfaces++;
    }

    /**
     * @return void
     */
    public function incrementAbstractClasses(): void
    {
        $this->abstractClasses++;
    }

    /**
     * @param  string  $name
     *
     * @return void
     */
    public function addConcreteFinalClasses(string $name): void
    {
        $this->concreteFinalClasses[] = $name;
    }

    /**
     * @param  string  $name
     *
     * @return void
     */
    public function incrementConcreteNonFinalClasses(string $name): void
    {
        $this->concreteNonFinalClasses[] = $name;
    }

    /**
     * @return void
     */
    public function incrementNonStaticMethods(): void
    {
        $this->nonStaticMethods++;
    }

    /**
     * @return void
     */
    public function incrementStaticMethods(): void
    {
        $this->staticMethods++;
    }

    /**
     * @return void
     */
    public function incrementPublicMethods(): void
    {
        $this->publicMethods++;
    }

    /**
     * @return void
     */
    public function incrementProtectedMethods(): void
    {
        $this->protectedMethods++;
    }

    /**
     * @return void
     */
    public function incrementPrivateMethods(): void
    {
        $this->privateMethods++;
    }

    /**
     * @return void
     */
    public function incrementNamedFunctions(): void
    {
        $this->namedFunctions++;
    }

    /**
     * @return void
     */
    public function incrementAnonymousFunctions(): void
    {
        $this->anonymousFunctions++;
    }

    /**
     * @return void
     */
    public function incrementClassConstants(): void
    {
        $this->classConstants++;
    }

    /**
     * @param  string  $name
     *
     * @return void
     */
    public function addGlobalConstant(string $name): void
    {
        $this->globalConstants[$this->currentFilename] = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function incrementTraits(): void
    {
        if ($this->currentFilename !== null) {
            $this->traits[] = $this->currentFilename;
        }
    }

    /**
     * @return int
     */
    public function getLines(): int
    {
        return $this->lines;
    }

    /**
     * @return int
     */
    public function getCommentLines(): int
    {
        return $this->commentLines;
    }

    /**
     * @return \SplFileInfo[]
     */
    public function getFiles(): array
    {
        return array_map(function (string $file) {
            return new SplFileInfo($file);
        }, $this->files);
    }

    /**
     * @return string[]
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @return string[]
     */
    public function getGlobalConstants(): array
    {
        return $this->globalConstants;
    }

    /**
     * @return string[]
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * @return int
     */
    public function getClassLines(): int
    {
        return (int) $this->getSum($this->classLines);
    }

    /**
     * @return array<string, float>
     */
    public function getPerClassLines(): array
    {
        return $this->classLines;
    }

    /**
     * @return string
     */
    public function getCurrentFilename()
    {
        return $this->currentFilename;
    }

    /**
     * @return int
     */
    public function getCurrentClassComplexity(): int
    {
        return $this->currentClassComplexity;
    }

    /**
     * @return int
     */
    public function getCurrentClassLines(): int
    {
        return $this->currentClassLines;
    }

    /**
     * @return int
     */
    public function getCurrentMethodComplexity(): int
    {
        return $this->currentMethodComplexity;
    }

    /**
     * @return int
     */
    public function getCurrentMethodLines(): int
    {
        return $this->currentMethodLines;
    }

    /**
     * @return int
     */
    public function getLogicalLines(): int
    {
        return $this->logicalLines;
    }

    /**
     * @return int
     */
    public function getMethodComplexity(): int
    {
        return $this->totalMethodComplexity;
    }

    /**
     * @return int
     */
    public function getClassConstants(): int
    {
        return $this->classConstants;
    }

    /**
     * @return mixed
     */
    public function getFunctionLines()
    {
        return $this->functionLines;
    }

    /**
     * @return mixed
     */
    public function getMethodLines()
    {
        return $this->methodLines;
    }

    /**
     * @return mixed
     */
    public function getStaticAttributeAccesses()
    {
        return $this->staticAttributeAccesses;
    }

    /**
     * @return mixed
     */
    public function getComplexity()
    {
        return $this->complexity;
    }

    /**
     * @return mixed
     */
    public function getPossibleConstantAccesses()
    {
        return $this->possibleConstantAccesses;
    }

    /**
     * @return mixed
     */
    public function getGlobalVariableAccesses()
    {
        return $this->globalVariableAccesses;
    }

    /**
     * @return mixed
     */
    public function getNonStaticMethodCalls()
    {
        return $this->nonStaticMethodCalls;
    }

    /**
     * @return mixed
     */
    public function getNonStaticAttributeAccesses()
    {
        return $this->nonStaticAttributeAccesses;
    }

    /**
     * @return mixed
     */
    public function getAnonymousFunctions()
    {
        return $this->anonymousFunctions;
    }

    /**
     * @return mixed
     */
    public function getNamedFunctions()
    {
        return $this->namedFunctions;
    }

    /**
     * @return mixed
     */
    public function getPublicMethods()
    {
        return $this->publicMethods;
    }

    /**
     * @return mixed
     */
    public function getStaticMethods()
    {
        return $this->staticMethods;
    }

    /**
     * @return mixed
     */
    public function getNonStaticMethods()
    {
        return $this->nonStaticMethods;
    }

    /**
     * @return mixed
     */
    public function getConcreteNonFinalClasses()
    {
        return $this->concreteNonFinalClasses;
    }

    /**
     * @return array
     */
    public function getConcreteFinalClasses(): array
    {
        return $this->concreteFinalClasses;
    }

    /**
     * @return mixed
     */
    public function getNamespaces()
    {
        return count($this->namespaces);
    }

    /**
     * @return array
     */
    public function getClassComplexity(): array
    {
        return $this->classComplexity;
    }

    /**
     * @return mixed
     */
    public function getProtectedMethods()
    {
        return $this->protectedMethods;
    }

    /**
     * @return int
     */
    public function getPrivateMethods(): int
    {
        return $this->privateMethods;
    }

    /**
     * @return mixed
     */
    public function getStaticMethodCalls()
    {
        return $this->staticMethodCalls;
    }

    /**
     * @return mixed
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @return int
     */
    public function getAbstractClasses(): int
    {
        return $this->abstractClasses;
    }

    /**
     * @return mixed
     */
    public function getSuperGlobalVariableAccesses()
    {
        return $this->superGlobalVariableAccesses;
    }

    /**
     * @return int
     */
    public function getNonCommentLines()
    {
        return $this->getLines() - $this->getCommentLines();
    }

    /**
     * @return float|int
     */
    public function getAverageClassLength()
    {
        return $this->getAverage($this->classLines);
    }

    /**
     * @param  array<float>  $array
     *
     * @return float
     */
    private function getAverage($array): float
    {
        return $this->divide($this->getSum($array), $this->getCount($array));
    }

    /**
     * @param  array  $array
     *
     * @return int
     */
    private function getCount(array $array): int
    {
        return count($array);
    }

    /**
     * Returns the sum value from the given array.
     *
     * @param  array  $array
     *
     * @return int|float
     */
    private function getSum(array $array)
    {
        return array_sum($array);
    }

    /**
     * Returns the maximum value from the given array.
     *
     * @param  array  $array
     *
     * @return int
     */
    private function getMaximum(array $array)
    {
        return max($array);
    }

    /**
     * @param  float  $x
     * @param  float  $y
     *
     * @return float
     */
    private function divide(float $x, float $y): float
    {
        return $y != 0 ? $x / $y : 0;
    }

    /**
     * @return int
     */
    public function getMaximumClassLength()
    {
        return $this->getMaximum($this->classLines);
    }

    /**
     * @return int
     */
    public function getAverageMethodLength(): int
    {
        return (int) $this->getAverage($this->methodLines);
    }

    /**
     * @return int
     */
    public function getMaximumMethodLength(): int
    {
        return $this->getMaximum($this->methodLines);
    }

    /**
     * @return int
     */
    public function getAverageFunctionLength(): int
    {
        return (int) $this->divide($this->getFunctionLines(), $this->getFunctions());
    }

    /**
     * @return int|mixed
     */
    public function getNotInClassesOrFunctions()
    {
        return $this->getLogicalLines() - $this->getClassLines() - $this->getFunctionLines();
    }

    /**
     * @return float|int
     */
    public function getAverageComplexityPerLogicalLine()
    {
        return $this->divide($this->getComplexity(), $this->getLogicalLines());
    }

    /**
     * @return float|int
     */
    public function getAverageComplexityPerClass()
    {
        return $this->getAverage($this->classComplexity);
    }

    /**
     * @return int
     */
    public function getMaximumClassComplexity()
    {
        return $this->getMaximum($this->getClassComplexity());
    }

    /**
     * @return float
     */
    public function getAverageComplexityPerMethod(): float
    {
        return $this->getAverage($this->methodComplexity);
    }

    /**
     * @return float
     */
    public function getMaximumMethodComplexity(): float
    {
        return $this->getMaximum($this->methodComplexity);
    }

    /**
     * @return int
     */
    public function getGlobalAccesses(): int
    {
        return $this->getGlobalConstantAccesses() + $this->getGlobalVariableAccesses() + $this->getSuperGlobalVariableAccesses();
    }

    /**
     * @return int
     */
    public function getGlobalConstantAccesses(): int
    {
        return count(\array_intersect($this->possibleConstantAccesses, $this->globalConstants));
    }

    /**
     * @return int
     */
    public function getAttributeAccesses(): int
    {
        return $this->getNonStaticAttributeAccesses() + $this->getStaticAttributeAccesses();
    }

    /**
     * @return int
     */
    public function getMethodCalls(): int
    {
        return $this->getNonStaticMethodCalls() + $this->getStaticMethodCalls();
    }

    /**
     * @return int
     */
    public function getClasses(): int
    {
        return $this->getAbstractClasses() + count($this->getConcreteNonFinalClasses()) + count($this->getConcreteFinalClasses());
    }

    /**
     * @return int
     */
    public function getMethods(): int
    {
        return $this->getNonStaticMethods() + $this->getStaticMethods();
    }

    /**
     * @return int
     */
    public function getFunctions(): int
    {
        return $this->getNamedFunctions() + $this->getAnonymousFunctions();
    }

    /**
     * @return int
     */
    public function getConstants(): int
    {
        return count($this->getGlobalConstants()) + $this->getClassConstants();
    }
}
