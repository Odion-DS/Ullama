<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Ollama extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ollama';
    }
}
