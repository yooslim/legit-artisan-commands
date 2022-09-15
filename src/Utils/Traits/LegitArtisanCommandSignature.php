<?php

namespace YOoSlim\LegitArtisanCommands\Utils\Traits;

trait LegitArtisanCommandSignature
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature .= '{--token= : Token for authentication }';
        
        parent::__construct();
    }
}
