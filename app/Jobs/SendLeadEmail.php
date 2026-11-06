<?php

namespace App\Jobs;

use App\Mail\LeadEmail;
use App\Models\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendLeadEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Lead $lead) {}

    public function handle(): void
    {
        Mail::to(config('auth.admin_email'))->send(new LeadEmail($this->lead));
    }
}
