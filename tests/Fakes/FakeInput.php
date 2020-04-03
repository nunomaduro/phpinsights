<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

final class FakeInput
{
    /**
     * @param array<string> $paths
     *
     * @return \Symfony\Component\Console\Input\ArrayInput
     */
    public static function paths(array $paths): ArrayInput
    {
        return new ArrayInput([
            'paths' => $paths,
        ], new InputDefinition([
            new InputArgument('paths'),
        ]));
    }
}
