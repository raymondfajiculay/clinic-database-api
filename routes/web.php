<?php

use App\Http\Controllers\ContraceptiveController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/client-record', [ContraceptiveController::class, 'client_record']);
Route::get('/fp-record', [ContraceptiveController::class, 'fp_record']);
Route::get('/injectable-record', [ContraceptiveController::class, 'injectable_record']);
Route::get('/implant-record', [ContraceptiveController::class, 'implant_record']);
Route::get('/iud-record', [ContraceptiveController::class, 'iud_record']);
Route::get('/pill-record', [ContraceptiveController::class, 'pill_record']);
Route::get('/condom-record', [ContraceptiveController::class, 'condom_record']);
Route::get('/supplement-record', [ContraceptiveController::class, 'supplement_record']);
Route::get('/services-record', [ContraceptiveController::class, 'services_record']);
Route::get('/prenatal-record', [ContraceptiveController::class, 'prenatal_record']);
