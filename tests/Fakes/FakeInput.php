<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

final class FakeInput
{
   public static function directory(string $directory): ArrayInput
   {
       return new ArrayInput([
           'directory' => $directory,
       ], new InputDefinition([
               new InputArgument('directory')
           ])
       );
   }
}
