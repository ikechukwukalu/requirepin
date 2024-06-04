<?php

use Illuminate\Support\Facades\Route;
use Ikechukwukalu\Requirepin\Controllers\PinController;

$authMiddleware = config('requiredpin.auth_middleware');

Route::middleware('auth:'.$authMiddleware)->group(function () {
    Route::post('change/pin', [PinController::class, 'changePin'])
        ->name('changePin');

    Route::post('pin/required/{uuid}', [PinController::class,
        'pinRequired'])->name('pinRequired');
});
