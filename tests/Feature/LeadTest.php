<?php

namespace Tests\Feature;

use App\Jobs\SendLeadEmail;
use App\Mail\LeadEmail;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '555-0100',
        'message' => 'I would like a new website.',
    ];

    public function test_a_lead_is_stored_and_the_email_job_is_queued(): void
    {
        Queue::fake();

        $this->postJson('/api/lead', $this->payload)
            ->assertOk()
            ->assertExactJson(['status' => 'success']);

        $this->assertDatabaseHas('leads', $this->payload + ['followed_up' => false]);
        Queue::assertPushed(SendLeadEmail::class);
    }

    public function test_the_lead_email_is_sent_to_the_admin(): void
    {
        config(['auth.admin_email' => 'admin@deltavcreative.com']);
        Mail::fake();

        $this->postJson('/api/lead', $this->payload)->assertOk();

        Mail::assertSent(LeadEmail::class, fn (LeadEmail $mail) => $mail->hasTo('admin@deltavcreative.com')
            && $mail->hasFrom('leads@deltavcreative.com')
            && $mail->hasSubject('DVC Website Lead'));
    }

    public function test_the_lead_email_renders_the_lead_details(): void
    {
        $lead = Lead::factory()->create($this->payload);

        (new LeadEmail($lead))
            ->assertSeeInHtml('Jane Doe')
            ->assertSeeInHtml('jane@example.com')
            ->assertSeeInHtml('555-0100')
            ->assertSeeInHtml('I would like a new website.');
    }

    public function test_required_fields_are_validated(): void
    {
        $this->postJson('/api/lead', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'phone']);

        $this->postJson('/api/lead', ['email' => 'not-an-email'] + $this->payload)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_production_accepts_requests_from_the_dvc_origin(): void
    {
        Queue::fake();
        $this->app['env'] = 'production';

        $this->withHeader('Origin', 'https://deltavcreative.com')
            ->postJson('/api/lead', $this->payload)
            ->assertOk();
    }

    public function test_production_rejects_requests_from_other_origins(): void
    {
        $this->app['env'] = 'production';

        $this->withHeader('Origin', 'https://evil.example')
            ->postJson('/api/lead', $this->payload)
            ->assertForbidden();

        $this->postJson('/api/lead', $this->payload)->assertForbidden();

        $this->assertDatabaseCount('leads', 0);
    }
}
