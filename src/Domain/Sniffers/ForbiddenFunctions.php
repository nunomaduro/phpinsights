<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Sniffers;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Sniffer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;

/**
 * @internal
 */
final class ForbiddenFunctions implements Sniffer
{
    /**
     * {@inheritDoc}
     */
    public function getSniff(): Sniff
    {
        $sniff = new ForbiddenFunctionsSniff();

        $sniff->forbiddenFunctions = array_merge($sniff->forbiddenFunctions, get_defined_functions()['user']);

        return $sniff;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(Collector $collector, array $error): void
    {
        $function = str_replace('The use of function', '', $error['message']);
        $function = trim(explode('()', $function)[0]);
        $collector->addGlobalFunctions((int) $error['line'], $function);
    }
}
