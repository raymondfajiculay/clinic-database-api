<?php

use App\Http\Controllers\ContraceptiveController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/client-information', [ContraceptiveController::class, 'client_record']);
Route::get('/fp_record', [ContraceptiveController::class, 'fp_record']);
Route::get('/injectable_record', [ContraceptiveController::class, 'injectable_record']);
Route::get('/implant_record', [ContraceptiveController::class, 'implant_record']);
Route::get('/iud_record', [ContraceptiveController::class, 'iud_record']);
Route::get('/pill_record', [ContraceptiveController::class, 'pill_record']);
Route::get('/condom_record', [ContraceptiveController::class, 'condom_record']);
Route::get('/supplement_record', [ContraceptiveController::class, 'supplement_record']);
Route::get('/services_record', [ContraceptiveController::class, 'services_record']);


