<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.admin_email' => 'admin@deltavcreative.com']);
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_the_admin_can_see_leads(): void
    {
        $admin = User::factory()->create(['email' => 'admin@deltavcreative.com']);
        Lead::factory()->create(['name' => 'Jane Doe']);

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/leads')->assertOk()->assertSee('Jane Doe');
        $this->actingAs($admin)->get('/admin/users')->assertOk();
    }

    public function test_other_users_are_forbidden(): void
    {
        $user = User::factory()->create(['email' => 'someone@example.com']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/leads')->assertForbidden();
    }

    public function test_nobody_gets_in_when_no_admin_email_is_configured(): void
    {
        config(['auth.admin_email' => null]);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }
}
