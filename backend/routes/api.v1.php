<?php

use Illuminate\Support\Facades\Route;
use FMS\Interface\Controllers\CompanyController;
use FMS\Interface\Controllers\FundController;
use FMS\Interface\Controllers\FundManagerController;

Route::controller(FundController::class)->group(function () {
    Route::get('/funds', 'list');
    Route::get('/funds/duplicated', 'listDuplicated');
    Route::post('/funds', 'create');
    Route::put('/funds/{id}', 'update');
    Route::delete('/funds/{id}', 'delete');
});

Route::controller(CompanyController::class)->group(function () {
    Route::get('/companies', 'list');
});

Route::controller(FundManagerController::class)->group(function () {
    Route::get('/fund-managers', 'list');
});
