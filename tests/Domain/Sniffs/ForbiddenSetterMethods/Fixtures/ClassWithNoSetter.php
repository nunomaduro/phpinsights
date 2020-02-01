<?php

namespace Tests\Domain\Insights\ForbiddenSetterMethods\Fixtures;

class ClassWithNoSetter
{
    public function changeName(string $newName)
    {
    }
}
