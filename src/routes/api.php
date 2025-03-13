<?php

use Ikechukwukalu\Requirepin\Controllers\PinController;
use Illuminate\Support\Facades\Route;

$authMiddleware = config('requirepin.auth_middleware');

Route::middleware("auth:$authMiddleware")->group(function () {
    Route::post('change/pin', [PinController::class, 'changePin'])
        ->name('changePin');

    Route::post('pin/required/{uuid}', [PinController::class, 'pinRequired'])
        ->name('pinRequired');
});
