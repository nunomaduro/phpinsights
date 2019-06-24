<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use function count;
use function max;

/**
 * @internal
 */
final class Collector
{
    /**
     * @var string
     */
    private $dir;

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
     * @var array<string>
     */
    private $files = [];

    /**
     * @var array<string>
     */
    private $directories = [];

    /**
     * @var array<string>
     */
    private $concreteNonFinalClasses = [];

    /**
     * @var array<string>
     */
    private $concreteFinalClasses = [];

    /**
     * @var array<string>
     */
    private $abstractClasses = [];

    /**
     * @var array<string>
     */
    private $traits = [];

    /**
     * @var array<string>
     */
    private $globalConstants = [];

    /**
     * @var int
     */
    private $interfaces = 0;

    /**
     * @var array<string>
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
     * @var array<int>
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
     * @var array<string, int>
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
     * @var array<string>
     */
    private $globalVariables = [];

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
     * @var array<string, array<string>>
     */
    private $namedFunctions = [];

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
     * @var array<string, string>
     */
    private $globalFunctions = [];

    /**
     * Creates a new instance of the Collector.
     *
     * @param  string  $dir
     */
    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @param  string  $filename
     *
     * @return void
     */
    public function addFile(string $filename): void
    {
        $filename = str_replace($this->dir . '/', '', $filename);

        $this->files[$filename] = $filename;
        $this->directories[] = \dirname($filename);
        $this->directories = array_unique($this->directories);
        $this->currentFilename = $filename;
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
     * @param  string  $name
     *
     * @return void
     */
    public function currentMethodStop(string $name): void
    {
        $this->methodComplexity[] = $this->currentMethodComplexity;
        $this->methodLines[$this->currentFilename . ':' . $name] = $this->currentMethodLines;
    }

    /**
     * @return void
     */
    public function incrementFunctionLines(): void
    {
        $this->functionLines++;
    }

    /**
     * Increase the complexity of the analysis
     *
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
     * @param  int  $line
     * @param  string  $name
     *
     * @return void
     */
    public function addGlobalFunctions(int $line, string $name): void
    {
        $this->globalFunctions[$this->currentFilename . ':' . $line] = $name;
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
     * Increment if calling non static method.
     *
     * @return void
     */
    public function incrementNonStaticMethodCalls(): void
    {
        $this->nonStaticMethodCalls++;
    }

    /**
     * Increment if a calling a static method
     *
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
     * Increment if class is a interface.
     *
     * @return void
     */
    public function incrementInterfaces(): void
    {
        $this->interfaces++;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function addAbstractClass(string $name): void
    {
        $this->abstractClasses[] = $name;
    }

    /**
     * @param  string  $name
     *
     * @return void
     */
    public function addConcreteFinalClass(string $name): void
    {
        $this->concreteFinalClasses[] = $name;
    }

    /**
     * @param  string  $name
     *
     * @return void
     */
    public function addConcreteNonFinalClass(string $name): void
    {
        $this->concreteNonFinalClasses[] = $name;
    }

    /**
     * Increment if method.
     *
     * @return void
     */
    public function incrementNonStaticMethods(): void
    {
        $this->nonStaticMethods++;
    }

    /**
     * Increment if static method.
     *
     * @return void
     */
    public function incrementStaticMethods(): void
    {
        $this->staticMethods++;
    }

    /**
     * Increment if public method.
     *
     * @return void
     */
    public function incrementPublicMethods(): void
    {
        $this->publicMethods++;
    }

    /**
     * Increment if protected method.
     *
     * @return void
     */
    public function incrementProtectedMethods(): void
    {
        $this->protectedMethods++;
    }

    /**
     * Increment if private method.
     *
     * @return void
     */
    public function incrementPrivateMethods(): void
    {
        $this->privateMethods++;
    }

    /**
     * @param  string  $name
     *
     * @return void
     */
    public function addNamedFunctions(string $name): void
    {
        if (! array_key_exists($this->currentFilename, $this->namedFunctions)) {
            $this->namedFunctions[$this->currentFilename] = [];
        }

        $this->namedFunctions[$this->currentFilename][] = $name;
    }

    /**
     * Increment if anonymous function.
     *
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

    public function incrementTraits(): void
    {
        if ($this->currentFilename !== null) {
            $this->traits[] = $this->currentFilename;
        }
    }

    /**
     * Returns the analysed dir.
     *
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * @return int
     */
    public function getLines(): int
    {
        return $this->getCommentLines()
            + $this->getFunctionLines()
            + $this->getClassLines()
            + $this->getNotInClassesOrFunctions();
    }

    /**
     * @return int
     */
    public function getCommentLines(): int
    {
        return $this->commentLines;
    }

    /**
     * @return array<string, string>
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return array<string>
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @return array<string>
     */
    public function getGlobalConstants(): array
    {
        return $this->globalConstants;
    }

    /**
     * @return array<string>
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
    public function getCurrentFilename(): string
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
     * @return int
     */
    public function getFunctionLines(): int
    {
        return $this->functionLines;
    }

    /**
     * @return array<string, int>
     */
    public function getMethodLines(): array
    {
        return $this->methodLines;
    }

    /**
     * @return array<string, string>
     */
    public function getGlobalFunctions(): array
    {
        return $this->globalFunctions;
    }

    /**
     * @return int
     */
    public function getStaticAttributeAccesses(): int
    {
        return $this->staticAttributeAccesses;
    }

    /**
     * Returns the complexity of the analysed data.
     *
     * @return int
     */
    public function getComplexity(): int
    {
        return $this->complexity;
    }

    /**
     * @return array<string>
     */
    public function getPossibleConstantAccesses(): array
    {
        return $this->possibleConstantAccesses;
    }

    /**
     * @return int
     */
    public function getGlobalVariableAccesses(): int
    {
        return $this->globalVariableAccesses;
    }

    /**
     * @return int
     */
    public function getNonStaticMethodCalls(): int
    {
        return $this->nonStaticMethodCalls;
    }

    /**
     * @return int
     */
    public function getNonStaticAttributeAccesses(): int
    {
        return $this->nonStaticAttributeAccesses;
    }

    /**
     * @return int
     */
    public function getAnonymousFunctions(): int
    {
        return $this->anonymousFunctions;
    }

    /**
     * @return array<string, array<string>>
     */
    public function getNamedFunctions(): array
    {
        return $this->namedFunctions;
    }

    /**
     * @return int
     */
    public function getPublicMethods(): int
    {
        return $this->publicMethods;
    }

    /**
     * @return int
     */
    public function getStaticMethods(): int
    {
        return $this->staticMethods;
    }

    /**
     * @return int
     */
    public function getNonStaticMethods(): int
    {
        return $this->nonStaticMethods;
    }

    /**
     * @return array<string>
     */
    public function getConcreteNonFinalClasses(): array
    {
        return $this->concreteNonFinalClasses;
    }

    /**
     * @return array<string>
     */
    public function getConcreteFinalClasses(): array
    {
        return $this->concreteFinalClasses;
    }

    /**
     * @return array<string>
     */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    /**
     * @return int
     */
    public function getProtectedMethods(): int
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
     * @return int
     */
    public function getStaticMethodCalls(): int
    {
        return $this->staticMethodCalls;
    }

    /**
     * @return int
     */
    public function getInterfaces(): int
    {
        return $this->interfaces;
    }

    /**
     * @return array<string>
     */
    public function getAbstractClasses(): array
    {
        return $this->abstractClasses;
    }

    /**
     * @return int
     */
    public function getSuperGlobalVariableAccesses(): int
    {
        return $this->superGlobalVariableAccesses;
    }

    /**
     * @return int
     */
    public function getNonCommentLines(): int
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
    private function getAverage(array $array): float
    {
        return $this->divide($this->getSum($array), $this->getCount($array));
    }

    /**
     * @param  array<string, float|int>  $array
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
     * @param  array<string, float|int>  $array
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
     * @param  array<string, float|int>  $array
     *
     * @return int|float
     */
    private function getMaximum(array $array)
    {
        return (bool) count($array) ? max($array) : 0;
    }

    /**
     * @param  float  $x
     * @param  float  $y
     *
     * @return float
     */
    private function divide(float $x, float $y): float
    {
        return $y !== 0.0 ? $x / $y : 0;
    }

    /**
     * @return int
     */
    public function getMaximumClassLength(): int
    {
        return (int) $this->getMaximum($this->classLines);
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
        return (int) $this->getMaximum($this->methodLines);
    }

    /**
     * @return int
     */
    public function getAverageFunctionLength(): int
    {
        return (int) $this->divide($this->getFunctionLines(), $this->getFunctions());
    }

    /**
     * @return int
     */
    public function getNotInClassesOrFunctions(): int
    {
        return $this->getLogicalLines() - $this->getClassLines() - $this->getFunctionLines();
    }

    /**
     * @return float
     */
    public function getAverageComplexityPerLogicalLine(): float
    {
        return $this->divide($this->getLogicalLines(), $this->getComplexity());
    }

    /**
     * @return float
     */
    public function getAverageComplexityPerClass(): float
    {
        return $this->getAverage($this->classComplexity);
    }

    /**
     * Return
     *
     * @return array<string, float>
     */
    public function getClassComplexity(): array
    {
        return $this->classComplexity;
    }

    /**
     * @return int
     */
    public function getMaximumClassComplexity(): int
    {
        return (int) $this->getMaximum($this->getClassComplexity());
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
     * Get the amount of calls to methods analysed.
     *
     * @return int
     */
    public function getMethodCalls(): int
    {
        return $this->getNonStaticMethodCalls() + $this->getStaticMethodCalls();
    }

    /**
     * Get the amount of classes analysed.
     *
     * @return int
     */
    public function getClasses(): int
    {
        return count($this->getAbstractClasses()) + count($this->getConcreteNonFinalClasses()) + count($this->getConcreteFinalClasses());
    }

    /**
     * Get the amount of methods analysed.
     *
     * @return int
     */
    public function getMethods(): int
    {
        return $this->getNonStaticMethods() + $this->getStaticMethods();
    }

    /**
     * Get the amount of functions analysed.
     *
     * @return int
     */
    public function getFunctions(): int
    {
        return count($this->getNamedFunctions()) + $this->getAnonymousFunctions();
    }

    /**
     * Get the amount of constants analysed.
     *
     * @return int
     */
    public function getConstants(): int
    {
        return count($this->getGlobalConstants()) + $this->getClassConstants();
    }
}
