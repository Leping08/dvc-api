<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected Lead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('leads@deltavcreative.com'),
            subject: 'DVC Website Lead',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.lead',
            with: ['lead' => $this->lead],
        );
    }
}
