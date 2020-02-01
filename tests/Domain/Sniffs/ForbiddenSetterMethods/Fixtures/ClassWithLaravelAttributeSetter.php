<?php

namespace Tests\Domain\Insights\ForbiddenSetterMethods\Fixtures;

class ClassWithLaravelAttributeSetter
{
    public function setNameAttribute(string $name)
    {
    }
}
