<?php

namespace NunoMaduro\PhpInsights\Domain;

use ReflectionMethod;
use SebastianBergmann\PHPLOC\Analyser as BaseAnalyser;

/**
 * Code originally taken from {SebastianBergmann\PHPLOC\Analyser}
 *
 * @method string    getNamespaceName(array $tolens, int $i)
 * @method bool      isClassDeclaration(array $tokens, int $i)
 * @method int|bool  getPreviousNonWhitespaceTokenPos(array $tokens, $start)
 * @method int|bool  getNextNonWhitespaceTokenPos(array $tokens, $start)
 * @method string    getClassName(string $namespace, array $tokens, int $i)
 *
 * @internal
 */
final class Analyser
{
    /**
     * @var array<string>
     */
    private $superGlobals = [
        '$_ENV',
        '$_POST',
        '$_GET',
        '$_COOKIE',
        '$_SERVER',
        '$_FILES',
        '$_REQUEST',
        '$HTTP_ENV_VARS',
        '$HTTP_POST_VARS',
        '$HTTP_GET_VARS',
        '$HTTP_COOKIE_VARS',
        '$HTTP_SERVER_VARS',
        '$HTTP_POST_FILES',
    ];

    /**
     * Processes a set of files.
     *
     * @param  string  $dir
     * @param  array<string>  $files
     *
     * @return \NunoMaduro\PhpInsights\Domain\Collector
     */
    public function analyse(string $dir, array $files): Collector
    {
        $dir = (string) realpath($dir);

        $collector = new Collector($dir);

        foreach ($files as $file) {
            $this->analyseFile($collector, $file);
        }

        return $collector;
    }

    /**
     * Processes a single file.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Collector  $collector
     * @param  string  $filename
     *
     * @return void
     */
    private function analyseFile(Collector $collector, string $filename): void
    {
        $buffer = (string) \file_get_contents($filename);
        $tokens = \token_get_all($buffer);
        $numTokens = \count($tokens);

        unset($buffer);

        $collector->addFile($filename);

        $blocks = [];
        $currentBlock = false;
        $namespace = false;
        $className = null;
        $functionName = null;
        $collector->currentClassReset();
        $isInMethod = false;

        for ($tokenIndex = 0; $tokenIndex < $numTokens; $tokenIndex++) {

            if (\is_string($tokens[$tokenIndex])) {
                $token = \trim($tokens[$tokenIndex]);

                if ($token === ';') {
                    if ($className !== null) {
                        $collector->currentClassIncrementLines();

                        if ($functionName !== null) {
                            $collector->currentMethodIncrementLines();
                        }
                    } elseif ($functionName !== null) {
                        $collector->incrementFunctionLines();
                    }

                    $collector->incrementLogicalLines();
                } elseif ($token === '?') {
                    if ($className !== null) {
                        $collector->currentClassIncrementComplexity();
                        $collector->currentMethodIncrementComplexity();
                    }

                    $collector->incrementComplexity();
                } elseif ($token === '{') {
                    if ($currentBlock === \T_CLASS) {
                        $block = $className;
                    } elseif ($currentBlock === \T_FUNCTION) {
                        $block = $functionName;
                    } else {
                        $block = false;
                    }

                    \array_push($blocks, $block);

                    $currentBlock = false;
                } elseif ($token === '}') {
                    $block = \array_pop($blocks);

                    if ($block !== false && $block !== null) {
                        if ($block === $functionName) {

                            if ($isInMethod) {
                                $collector->currentMethodStop($functionName);
                                $isInMethod = false;
                            }
                            $functionName = null;
                        } elseif ($block === $className) {
                            $className = null;
                            $collector->currentClassReset();
                        }
                    }
                }

                continue;
            }

            [$token, $value] = $tokens[$tokenIndex];

            switch ($token) {
                case \T_NAMESPACE:
                    $namespace = $this->getNamespaceName($tokens, $tokenIndex);
                    $collector->addNamespace($namespace);

                    break;

                case \T_CLASS:
                case \T_INTERFACE:
                case \T_TRAIT:
                    if (! $this->isClassDeclaration($tokens, $tokenIndex)) {
                        break;
                    }

                    $collector->currentClassReset();
                    $collector->currentClassIncrementComplexity();
                    $className = $this->getClassName((string) $namespace, $tokens, $tokenIndex);
                    $currentBlock = \T_CLASS;

                    if ($token === \T_TRAIT) {
                        $collector->incrementTraits();
                    } elseif ($token === \T_INTERFACE) {
                        $collector->incrementInterfaces();
                    } else {
                        if (isset($tokens[$tokenIndex - 2]) &&
                            \is_array($tokens[$tokenIndex - 2])) {
                            if ($tokens[$tokenIndex - 2][0] === \T_ABSTRACT) {
                                $collector->addAbstractClass($filename);
                            } elseif ($tokens[$tokenIndex - 2][0] === \T_FINAL) {
                                $collector->addConcreteFinalClass($filename);
                            } else {
                                $collector->addConcreteNonFinalClass($filename);
                            }
                        } else {
                            $collector->addConcreteNonFinalClass($filename);
                        }
                    }

                    break;

                case \T_FUNCTION:
                    $prev = $this->getPreviousNonWhitespaceTokenPos($tokens, $tokenIndex);

                    if ($tokens[$prev][0] === \T_USE) {
                        break;
                    }

                    $currentBlock = \T_FUNCTION;

                    $next = $this->getNextNonWhitespaceTokenPos($tokens, $tokenIndex);

                    if (! \is_array($tokens[$next]) && $tokens[$next] === '&') {
                        $next = $this->getNextNonWhitespaceTokenPos($tokens, $next);
                    }

                    if (\is_array($tokens[$next]) &&
                        $tokens[$next][0] === \T_STRING) {
                        $functionName = $tokens[$next][1];
                    } else {
                        $currentBlock = 'anonymous function';
                        $functionName = 'anonymous function';
                        $collector->incrementAnonymousFunctions();
                    }

                    if ($currentBlock === \T_FUNCTION) {
                        if ($className === null &&
                            $functionName != 'anonymous function') {
                            $collector->addNamedFunctions($functionName);
                        } else {
                            $static = false;
                            $visibility = \T_PUBLIC;

                            for ($j = $tokenIndex; $j > 0; $j--) {
                                if (\is_string($tokens[$j])) {
                                    if ($tokens[$j] === '{' ||
                                        $tokens[$j] === '}' ||
                                        $tokens[$j] === ';') {
                                        break;
                                    }

                                    continue;
                                }

                                if (isset($tokens[$j][0])) {
                                    switch ($tokens[$j][0]) {
                                        case \T_PRIVATE:
                                            $visibility = \T_PRIVATE;

                                            break;

                                        case \T_PROTECTED:
                                            $visibility = \T_PROTECTED;

                                            break;

                                        case \T_STATIC:
                                            $static = true;

                                            break;
                                    }
                                }
                            }

                            $isInMethod = true;
                            $collector->currentMethodStart();

                            if (! $static) {
                                $collector->incrementNonStaticMethods();
                            } else {
                                $collector->incrementStaticMethods();
                            }

                            if ($visibility === \T_PUBLIC) {
                                $collector->incrementPublicMethods();
                            } elseif ($visibility === \T_PROTECTED) {
                                $collector->incrementProtectedMethods();
                            } else {
                                $collector->incrementPrivateMethods();
                            }
                        }
                    }

                    break;

                case \T_CURLY_OPEN:
                    $currentBlock = \T_CURLY_OPEN;
                    \array_push($blocks, $currentBlock);

                    break;

                case \T_DOLLAR_OPEN_CURLY_BRACES:
                    $currentBlock = \T_DOLLAR_OPEN_CURLY_BRACES;
                    \array_push($blocks, $currentBlock);

                    break;

                case \T_IF:
                case \T_ELSEIF:
                case \T_FOR:
                case \T_FOREACH:
                case \T_WHILE:
                case \T_CASE:
                case \T_CATCH:
                case \T_BOOLEAN_AND:
                case \T_LOGICAL_AND:
                case \T_BOOLEAN_OR:
                case \T_LOGICAL_OR:
                    if ($isInMethod) {
                        $collector->currentClassIncrementComplexity();
                        $collector->currentMethodIncrementComplexity();
                    }

                    $collector->incrementComplexity();

                    break;

                case \T_COMMENT:
                case \T_DOC_COMMENT:
                    $collector->incrementCommentLines(\substr_count(\rtrim($value, "\n"), "\n") + 1);

                    break;
                case \T_CONST:
                    $collector->incrementClassConstants();

                    break;

                case \T_STRING:
                    if ($value === 'define'
                        && $tokens[$tokenIndex - 1][1] !== "::"
                        && $tokens[$tokenIndex - 1][1] !== "->"
                        && (! isset($tokens[$tokenIndex - 2][1]) || $tokens[$tokenIndex - 2][1] !== 'function')) {

                        $j = $tokenIndex + 1;

                        while (isset($tokens[$j]) && $tokens[$j] != ';') {
                            if (\is_array($tokens[$j]) &&
                                $tokens[$j][0] === \T_CONSTANT_ENCAPSED_STRING) {

                                $collector->addGlobalConstant(\str_replace('\'', '', $tokens[$j][1]));

                                break;
                            }

                            $j++;
                        }
                    } else {
                        $collector->addPossibleConstantAccesses($value);
                    }

                    break;

                case \T_DOUBLE_COLON:
                case \T_OBJECT_OPERATOR:
                    $n = $this->getNextNonWhitespaceTokenPos($tokens, $tokenIndex);
                    $nn = $this->getNextNonWhitespaceTokenPos($tokens, $n);

                    if ((bool) $n && (bool) $nn &&
                        isset($tokens[$n][0]) &&
                        ($tokens[$n][0] === \T_STRING ||
                            $tokens[$n][0] === \T_VARIABLE) &&
                        $tokens[$nn] === '(') {
                        if ($token === \T_DOUBLE_COLON) {
                            $collector->incrementStaticMethodCalls();
                        } else {
                            $collector->incrementNonStaticMethodCalls();
                        }
                    } else {
                        if ($token === \T_DOUBLE_COLON &&
                            $tokens[$n][0] === \T_VARIABLE) {
                            $collector->incrementStaticAttributeAccesses();
                        } elseif ($token === \T_OBJECT_OPERATOR) {
                            $collector->incrementNonStaticAttributeAccesses();
                        }
                    }

                    break;

                case \T_GLOBAL:
                    $collector->incrementGlobalVariableAccesses();

                    break;

                case \T_VARIABLE:
                    if ($value === '$GLOBALS') {
                        $collector->incrementGlobalVariableAccesses();
                    } elseif (isset(array_flip($this->superGlobals)[$value])) {
                        $collector->incrementSuperGlobalVariableAccesses();
                    }

                    break;
            }
        }
    }

    /**
     * @param  string  $method
     * @param  array<int|float|array<string>,int|float|array<string>>  $args
     *
     * @return int|float|array<string>
     */
    public function __call(string $method, array $args)
    {
        $method = new ReflectionMethod(BaseAnalyser::class, $method);
        $method->setAccessible(true);

        return $method->invoke(new BaseAnalyser(), ...$args);
    }
}
