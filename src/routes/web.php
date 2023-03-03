<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Ikechukwukalu\Requirepin\Controllers\PinController;

Route::middleware('auth')->group(function () {
    Route::post('change/pin', [PinController::class, 'changePin'])
        ->name('changePinWeb');

    Route::post('pin/required/{uuid}', [PinController::class,
        'pinRequired'])->name('pinRequiredWeb');

    Route::get('change/pin', [PinController::class, 'changePinView'])
        ->name('changePinView');

    Route::get('pin/required/{uuid?}', [PinController::class, 'requirePinView'])
        ->name('requirePinView');
});
