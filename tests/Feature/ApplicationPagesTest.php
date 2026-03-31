<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_is_displayed(): void
    {
        $this->get('/')
            ->assertOk();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_board_requires_authentication(): void
    {
        $this->get('/board')
            ->assertRedirect('/login');
    }

    public function test_dashboard_requires_verified_email(): void
    {
        config()->set('auth.require_verified_email', true);

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('verification.notice', absolute: false));
    }

    public function test_unverified_user_can_access_dashboard_when_verification_requirement_is_disabled(): void
    {
        config()->set('auth.require_verified_email', false);

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_verified_user_can_view_dashboard_and_only_their_applications_are_shown(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $visibleApplication = Application::factory()->for($user)->create([
            'company' => 'Visible Company',
            'position' => 'Backend Engineer',
            'status' => 'applied',
            'stage' => 'applied',
        ]);

        Application::factory()->for($otherUser)->create([
            'company' => 'Hidden Company',
            'position' => 'Hidden Role',
            'status' => 'applied',
            'stage' => 'applied',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSeeText($visibleApplication->company)
            ->assertDontSeeText('Hidden Company')
            ->assertDontSeeText('Switch to vertical view');
    }

    public function test_verified_user_can_access_board_page_with_kanban_content(): void
    {
        $user = User::factory()->create();

        Application::factory()->for($user)->create([
            'company' => 'Board Company',
            'position' => 'Product Engineer',
            'status' => 'applied',
            'stage' => 'applied',
        ]);

        $this->actingAs($user)
            ->get('/board')
            ->assertOk()
            ->assertSeeText('Switch to vertical view')
            ->assertSeeText('Board Company');
    }

    public function test_dashboard_escapes_stored_content(): void
    {
        $user = User::factory()->create();
        $payload = '<script>alert("xss")</script>';

        Application::factory()->for($user)->create([
            'company' => $payload,
            'position' => 'Security Engineer',
            'status' => 'applied',
            'stage' => 'applied',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response
            ->assertOk()
            ->assertDontSee($payload, false);

        // Accept safe output in either HTML context or JSON-escaped context.
        $this->assertMatchesRegularExpression(
            '/(?:&lt;script&gt;alert\(&quot;xss&quot;\)&lt;\/script&gt;|\\\\u003Cscript\\\\u003Ealert\(\\\\u0022xss\\\\u0022\)\\\\u003C\\\\\/script\\\\u003E)/',
            $response->getContent()
        );
    }

    public function test_settings_page_is_available_only_to_authenticated_users(): void
    {
        $this->get('/settings')
            ->assertRedirect('/login');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/settings')
            ->assertOk()
            ->assertSeeText('Archiving Rules');
    }

    public function test_create_application_page_requires_authentication(): void
    {
        $this->get('/applications/create')
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_can_open_create_application_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('applications.create'))
            ->assertOk()
            ->assertSeeText('Create application')
            ->assertSeeText('New application');
    }

    public function test_authenticated_user_can_store_new_application_from_create_page(): void
    {
        $user = User::factory()->create();

        $payload = [
            'company' => 'Stripe',
            'position' => 'Backend Engineer',
            'city' => 'Sao Paulo',
            'location' => 'Remote',
            'applied_at' => '2026-03-27',
            'job_url' => 'https://example.com/jobs/stripe-backend',
            'personal_score' => 8.5,
            'salary_offered' => 14000,
            'salary_expected' => 16000,
        ];

        $this->actingAs($user)
            ->post(route('applications.store'), $payload)
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'company' => 'Stripe',
            'position' => 'Backend Engineer',
            'status' => 'applied',
            'stage' => 'applied',
            'location' => 'Remote',
        ]);
    }
}