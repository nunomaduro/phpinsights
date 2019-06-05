<?php

namespace Tests\Feature\Laravel\Fixtures;

use Illuminate\Database\Eloquent\Model;

class ModelWithAttributeSetter extends Model
{
    public function setNameAttribute(string $name) : void
    {

    }
}
