<?php

use App\Mail\LeadEmail;
use App\Models\Lead;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Lead email preview. Local only: it renders a real lead's contact details.
if (app()->environment('local')) {
    Route::get('/test', function () {
        return new LeadEmail(Lead::firstOrFail());
    });
}
