<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ApplicationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_is_authorized_for_application_actions(): void
    {
        $owner = User::factory()->create();
        $application = Application::factory()->for($owner)->create();

        $this->assertTrue(Gate::forUser($owner)->allows('view', $application));
        $this->assertTrue(Gate::forUser($owner)->allows('update', $application));
        $this->assertTrue(Gate::forUser($owner)->allows('delete', $application));
        $this->assertTrue(Gate::forUser($owner)->allows('move', $application));
        $this->assertTrue(Gate::forUser($owner)->allows('scheduleInterview', $application));
        $this->assertTrue(Gate::forUser($owner)->allows('toggleFavorite', $application));
    }

    public function test_non_owner_is_denied_for_application_actions(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $application = Application::factory()->for($owner)->create();

        $this->assertFalse(Gate::forUser($otherUser)->allows('view', $application));
        $this->assertFalse(Gate::forUser($otherUser)->allows('update', $application));
        $this->assertFalse(Gate::forUser($otherUser)->allows('delete', $application));
        $this->assertFalse(Gate::forUser($otherUser)->allows('move', $application));
        $this->assertFalse(Gate::forUser($otherUser)->allows('scheduleInterview', $application));
        $this->assertFalse(Gate::forUser($otherUser)->allows('toggleFavorite', $application));
    }
}
