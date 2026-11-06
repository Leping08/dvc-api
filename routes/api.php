<?php

use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Route;

Route::middleware('dvc_domain')->group(function () {
    Route::post('/lead', [LeadController::class, 'store']);
});
