<?php

namespace App\Services;

use App\Actions\CreateApplication;
use App\Actions\MoveApplication;
use App\Actions\ScheduleInterview;
use App\Actions\UpdateApplication;
use App\Concerns\DetectsApplicationColumns;
use App\Models\Application;
use App\Repositories\ApplicationRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

/**
 * Orchestrates all Application business operations.
 * The Livewire component delegates every side-effectful operation here,
 * keeping itself responsible only for UI state and validation.
 */
class ApplicationService
{
    use DetectsApplicationColumns;

    public function __construct(
        private readonly ApplicationRepository $repository,
        private readonly CreateApplication     $createAction,
        private readonly UpdateApplication     $updateAction,
        private readonly MoveApplication       $moveAction,
        private readonly ScheduleInterview     $scheduleInterviewAction,
    ) {}

    public function create(array $data): Application
    {
        Gate::authorize('create', Application::class);

        return $this->createAction->execute($data, Auth::id());
    }

    public function update(Application $application, array $data): void
    {
        Gate::authorize('update', $application);

        $this->updateAction->execute($application, $data);
    }

    public function delete(Application $application): void
    {
        Gate::authorize('delete', $application);

        $application->delete();
    }

    public function move(Application $application, string $status): void
    {
        Gate::authorize('move', $application);

        $this->moveAction->execute($application, $status);
    }

    public function scheduleInterview(Application $application, array $data): void
    {
        Gate::authorize('scheduleInterview', $application);

        $this->scheduleInterviewAction->execute($application, $data);
    }

    public function toggleFavorite(Application $application): void
    {
        Gate::authorize('toggleFavorite', $application);

        $application->is_favorite = !$application->is_favorite;
        $application->save();
    }

    public function allForUser(int $userId): Collection
    {
        return $this->repository->allForUser($userId);
    }

    public function findForUser(int $id, int $userId): ?Application
    {
        return $this->repository->findForUser($id, $userId);
    }

    /**
     * Archives expired applications for the given user, but at most once per day.
     * A cache flag keyed per user expires at midnight, ensuring a single daily sweep.
     */
    public function archiveExpiredIfDue(int $userId): void
    {
        $cacheKey = "archive_sweep_{$userId}";

        if (Cache::has($cacheKey)) {
            return;
        }

        $threshold = Carbon::today()->subDays($this->resolveArchiveDays());

        $this->repository->archiveExpired($userId, $threshold, $this->hasStageColumn());

        Cache::put($cacheKey, true, Carbon::now()->secondsUntilEndOfDay());
    }

    private function resolveArchiveDays(): int
    {
        $configured = (int) (Auth::user()?->archive_after_days ?: 30);

        return max(1, min(365, $configured));
    }
}
