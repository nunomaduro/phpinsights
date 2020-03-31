<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

final class FakeInput
{
    /**
     * @param array<string> $directories
     *
     * @return \Symfony\Component\Console\Input\ArrayInput
     */
    public static function directory(array $directories): ArrayInput
    {
        return new ArrayInput([
            'directories' => $directories,
        ], new InputDefinition([
            new InputArgument('directories'),
        ]));
    }
}
