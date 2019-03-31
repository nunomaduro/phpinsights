<?php

declare(strict_types=1);

use Narration\Container\Container;

return Container::makeWithInjectors([
    NunoMaduro\PhpInsights\Application\Injectors\Repositories::class,
]);
