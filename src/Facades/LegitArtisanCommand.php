<?php

namespace YOoSlim\LegitArtisanCommands\Facades;

use Illuminate\Support\Facades\Facade;

class LegitArtisanCommand extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'legitArtisanCommand';
    }
}