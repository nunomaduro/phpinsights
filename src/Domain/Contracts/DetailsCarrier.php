<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Domain\Details;

/**
 * @internal
 */
interface DetailsCarrier extends HasDetails
{
    public function addDetails(Details $details): void;
}
