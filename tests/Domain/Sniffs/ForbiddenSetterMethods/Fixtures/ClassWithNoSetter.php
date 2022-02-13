<?php

namespace Tests\Domain\Sniffs\ForbiddenSetterMethods\Fixtures;

class ClassWithNoSetter
{
    public function changeName(string $newName)
    {

    }
}
