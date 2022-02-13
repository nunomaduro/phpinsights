<?php

namespace Tests\Domain\Sniffs\ForbiddenSetterMethods\Fixtures;

class ClassWithLaravelAttributeSetter
{
    public function setNameAttribute(string $name)
    {

    }
}
