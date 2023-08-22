<?php

use Illuminate\Support\Facades\Route;
use Ikechukwukalu\Requirepin\Controllers\PinController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('change/pin', [PinController::class, 'changePin'])
        ->name('changePin');

    Route::post('pin/required/{uuid}', [PinController::class,
        'pinRequired'])->name('pinRequired');
});
