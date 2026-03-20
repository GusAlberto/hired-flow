<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsArchivingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_archiving_settings(): void
    {
        $user = User::factory()->create(['archive_after_days' => 30]);

        $this->actingAs($user)
            ->patch('/settings/archiving', ['archive_after_days' => 14])
            ->assertRedirect(route('settings.index', ['tab' => 'archiving'], false))
            ->assertSessionHas('status', 'Archive settings updated successfully.');

        $this->assertSame(14, $user->fresh()->archive_after_days);
    }

    public function test_invalid_archiving_setting_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/settings')
            ->patch('/settings/archiving', ['archive_after_days' => 0])
            ->assertRedirect('/settings')
            ->assertSessionHasErrors('archive_after_days');
    }

    public function test_manual_archiving_only_archives_the_current_users_expired_applications(): void
    {
        Carbon::setTestNow('2026-03-12 09:00:00');

        $user = User::factory()->create(['archive_after_days' => 7]);
        $otherUser = User::factory()->create(['archive_after_days' => 7]);

        $expiredOwnApplication = Application::factory()->for($user)->create([
            'status' => 'waiting',
            'stage' => 'waiting',
            'applied_at' => '2026-03-01',
        ]);

        $recentOwnApplication = Application::factory()->for($user)->create([
            'status' => 'applied',
            'stage' => 'applied',
            'applied_at' => '2026-03-10',
        ]);

        $otherUsersExpiredApplication = Application::factory()->for($otherUser)->create([
            'status' => 'offer',
            'stage' => 'offer',
            'applied_at' => '2026-03-01',
        ]);

        $this->actingAs($user)
            ->post('/settings/archiving/run-now')
            ->assertRedirect(route('settings.index', ['tab' => 'archiving'], false))
            ->assertSessionHas('status', 'Manual archive completed. 1 application(s) archived.');

        $this->assertSame('archived', $expiredOwnApplication->fresh()->status);
        $this->assertSame('archived', $expiredOwnApplication->fresh()->stage);
        $this->assertSame('applied', $recentOwnApplication->fresh()->status);
        $this->assertSame('offer', $otherUsersExpiredApplication->fresh()->status);

        Carbon::setTestNow();
    }
}