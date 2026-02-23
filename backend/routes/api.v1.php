<?php

use Illuminate\Support\Facades\Route;
use FMS\Interface\Controllers\FundController;

Route::controller(FundController::class)->group(function () {
    Route::get('/funds', 'list');
    Route::get('/funds/duplicated', 'listDuplicated');
    Route::post('/funds', 'create');
    Route::put('/funds/{id}', 'update');
    Route::delete('/funds/{id}', 'delete');
});
