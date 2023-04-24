<?php

use App\Http\Controllers\Api\V2\LevelController;
use Illuminate\Support\Facades\Route;

Route::controller(LevelController::class)->prefix('levels')->group(function() {
    Route::get('/', 'index');
    Route::get('/{level}', 'show');
    Route::get('/{level}/general-info', 'getGeneralInfo');
    Route::get('/{level}/gem', 'gem');
    Route::get('/{level}/gift', 'gift');
    Route::get('/{level}/licenses', 'licenses');
    Route::get('/{level}/prize', 'prizes');
});
