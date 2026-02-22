<?php

use Illuminate\Support\Facades\Route;
use FMS\Interface\Controllers\FundController;

Route::controller(FundController::class)->group(function () {
    Route::get('/funds', 'list');
    Route::post('/funds', 'create');
});
