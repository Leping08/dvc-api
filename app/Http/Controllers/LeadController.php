<?php

namespace App\Http\Controllers;

use App\Jobs\SendLeadEmail;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'email|required|max:255',
            'phone' => 'required|max:255',
            'message' => 'max:5000',
        ]);

        $lead = Lead::create($validated);

        SendLeadEmail::dispatch($lead);

        return ['status' => 'success'];
    }
}
