<?php

namespace Tests\Feature;

use App\Livewire\ApplicationsBoard;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicationsBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_new_application_from_the_board(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ApplicationsBoard::class)
            ->set('company', 'Acme Inc')
            ->set('position', 'Platform Engineer')
            ->set('city', 'Recife')
            ->set('location', 'Remote')
            ->set('applied_at', '2026-03-12')
            ->set('job_url', 'https://example.com/jobs/1')
            ->set('personal_score', 9)
            ->set('salary_offered', 8500)
            ->set('salary_expected', 9500)
            ->call('saveApplication')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'company' => 'Acme Inc',
            'position' => 'Platform Engineer',
            'status' => 'applied',
            'stage' => 'applied',
            'is_favorite' => 0,
        ]);
    }

    public function test_user_can_update_move_favorite_and_schedule_interview_for_their_application(): void
    {
        $user = User::factory()->create();
        $application = Application::factory()->for($user)->create([
            'company' => 'Before Co',
            'position' => 'PHP Developer',
            'status' => 'applied',
            'stage' => 'applied',
            'is_favorite' => false,
        ]);

        Livewire::actingAs($user)
            ->test(ApplicationsBoard::class)
            ->call('editApplication', $application->id)
            ->set('editCompany', 'After Co')
            ->set('editPosition', 'Senior PHP Developer')
            ->set('editCity', 'Sao Paulo')
            ->set('editLocation', 'Hybrid')
            ->set('editAppliedAt', '2026-03-05')
            ->set('editJobUrl', 'https://example.com/jobs/2')
            ->set('editPersonalScore', 10)
            ->set('editSalaryOffered', 12000)
            ->set('editSalaryExpected', 13000)
            ->set('editNotes', 'Strong opportunity')
            ->call('updateApplication')
            ->assertHasNoErrors()
            ->call('moveApplication', $application->id, 'offer')
            ->call('toggleFavorite', $application->id)
            ->call('prepareInterviewMove', $application->id)
            ->set('interviewDate', '2026-03-20')
            ->set('interviewTime', '14:30')
            ->set('interviewLocation', 'Google Meet')
            ->set('interviewIsRemote', true)
            ->set('interviewPlatform', 'Google Meet')
            ->call('saveInterviewMove')
            ->assertHasNoErrors();

        $application->refresh();

        $this->assertSame('After Co', $application->company);
        $this->assertSame('Senior PHP Developer', $application->position);
        $this->assertSame('Sao Paulo', $application->city);
        $this->assertSame('Hybrid', $application->location);
        $this->assertSame('interview', $application->status);
        $this->assertSame('interview', $application->stage);
        $this->assertTrue($application->is_favorite);
        $this->assertSame('Strong opportunity', $application->notes);
        $this->assertSame('2026-03-20', $application->interview_date?->format('Y-m-d'));
        $this->assertSame('14:30:00', $application->interview_time);
        $this->assertTrue($application->interview_is_remote);
        $this->assertSame('Google Meet', $application->interview_platform);
        $this->assertNull($application->interview_address);
    }

    public function test_user_cannot_delete_another_users_application(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $foreignApplication = Application::factory()->for($otherUser)->create();

        Livewire::actingAs($user)
            ->test(ApplicationsBoard::class)
            ->call('deleteApplication', $foreignApplication->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('applications', [
            'id' => $foreignApplication->id,
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_invalid_move_status_is_ignored(): void
    {
        $user = User::factory()->create();
        $application = Application::factory()->for($user)->create([
            'status' => 'applied',
            'stage' => 'applied',
        ]);

        Livewire::actingAs($user)
            ->test(ApplicationsBoard::class)
            ->call('moveApplication', $application->id, 'DROP TABLE applications')
            ->assertHasNoErrors();

        $this->assertSame('applied', $application->fresh()->status);
        $this->assertSame('applied', $application->fresh()->stage);
    }
}