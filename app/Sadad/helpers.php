<?php

use App\Sadad\Sadad;

if (! function_exists('sadad')) {
    function sadad(): Sadad
    {
        return new Sadad();
    }
}
