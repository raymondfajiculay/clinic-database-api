<?php

use App\Http\Controllers\ClientRecordController;
use App\Http\Controllers\IndicatorController;
use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([ApiKeyMiddleware::class])->group(function () {
     // Client Records
    Route::get('/client-record', [ClientRecordController::class, 'client_record']);
    Route::get('/fp-record', [ClientRecordController::class, 'fp_record']);
    Route::get('/injectable-record', [ClientRecordController::class, 'injectable_record']);
    Route::get('/implant-record', [ClientRecordController::class, 'implant_record']);
    Route::get('/iud-record', [ClientRecordController::class, 'iud_record']);
    Route::get('/pill-record', [ClientRecordController::class, 'pill_record']);
    Route::get('/condom-record', [ClientRecordController::class, 'condom_record']);
    Route::get('/supplement-record', [ClientRecordController::class, 'supplement_record']);
    Route::get('/services-record', [ClientRecordController::class, 'services_record']);
    Route::get('/prenatal-record', [ClientRecordController::class, 'prenatal_record']);
    
    // Indicators
    Route::get('/contraceptive-users', [IndicatorController::class, 'contraceptive_users']);
    Route::get('/contraceptive-referrals', [IndicatorController::class, 'contraceptive_referrals']);
    Route::get('/barangay-accessing-services', [IndicatorController::class, 'barangay_accessing_services']);
    Route::get('/screened-for-hiv', [IndicatorController::class, 'screened_for_hiv']);
    Route::get('/couple-years-protected', [IndicatorController::class, 'couple_years_protected']);
    Route::get('/couple-years-protected-youth', [IndicatorController::class, 'couple_years_protected_youth']);
    Route::get('/modern-contraceptive-user', [IndicatorController::class, 'modern_contraceptive_user']);
    Route::get('/hiv-screening', [IndicatorController::class, 'hiv_screening']);
    Route::get('/hiv-screening-reactive', [IndicatorController::class, 'hiv_screening_reactive']);
    Route::get('/pregnant-client', [IndicatorController::class, 'pregnant_client']);
    Route::get('/prenatal-checkup', [IndicatorController::class, 'prenatal_checkup']);
    Route::get('/community-pregnant-client', [IndicatorController::class, 'community_pregnant_client']);
    Route::get('/new-users', [IndicatorController::class, 'new_users']);
    Route::get('/hiv-referral', [IndicatorController::class, 'hiv_referral']);
    Route::get('/implant-records', [IndicatorController::class, 'implant_records']);
    Route::get('/women-provided-services', [IndicatorController::class, 'women_provided_services']);
    
});

