<?php

use Ikechukwukalu\Requirepin\Controllers\PinController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('requirepin.auth_route_guard', 'auth'))->group(function () {
    Route::post('change/pin', [PinController::class, 'changePin'])
        ->name('changePinWeb');

    Route::post('pin/required/{uuid}', [PinController::class, 'pinRequired'])
        ->name('pinRequiredWeb');

    Route::get('change/pin', [PinController::class, 'changePinView'])
        ->name('changePinView');

    Route::get('pin/required/{uuid?}', [PinController::class, 'requirePinView'])
        ->name('requirePinView');
});
