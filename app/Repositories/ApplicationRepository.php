<?php

namespace App\Repositories;

use App\Concerns\DetectsApplicationColumns;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Handles all direct database access for the Application model.
 * Business rules and orchestration belong in ApplicationService.
 */
class ApplicationRepository
{
    use DetectsApplicationColumns;

    /**
     * Returns all applications for a user, ordered by most recently updated.
     */
    public function allForUser(int $userId): Collection
    {
        $query = Application::where('user_id', $userId);

        if ($this->hasSortOrderColumn()) {
            $query->orderBy('sort_order')->orderByDesc('updated_at');
        } else {
            $query->latest('updated_at');
        }

        return $query->get();
    }

    public function reorderForUserStatus(int $userId, string $status, array $orderedIds): void
    {
        if (!$this->hasSortOrderColumn()) {
            return;
        }

        $normalizedIds = array_values(array_unique(array_map('intval', $orderedIds)));

        if (empty($normalizedIds)) {
            return;
        }

        $existingIds = Application::query()
            ->where('user_id', $userId)
            ->where('status', $status)
            ->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($existingIds)) {
            return;
        }

        $existingLookup = array_flip($existingIds);

        $validOrderedIds = array_values(array_filter(
            $normalizedIds,
            fn ($id) => isset($existingLookup[$id])
        ));

        $remainingIds = array_values(array_filter(
            $existingIds,
            fn ($id) => !in_array($id, $validOrderedIds, true)
        ));

        $finalOrder = array_merge($validOrderedIds, $remainingIds);

        DB::transaction(function () use ($finalOrder) {
            foreach ($finalOrder as $index => $applicationId) {
                Application::query()
                    ->where('id', $applicationId)
                    ->update(['sort_order' => $index + 1]);
            }
        });
    }

    /**
     * Finds a single application scoped to a specific user.
     * Returns null when not found or unauthorised.
     */
    public function findForUser(int $id, int $userId): ?Application
    {
        return Application::where('user_id', $userId)->find($id);
    }

    /**
     * Bulk-archives all active applications whose applied_at is strictly
     * before the threshold date.
     */
    public function archiveExpired(int $userId, Carbon $thresholdDate, bool $hasStageColumn): void
    {
        $updateData = ['status' => 'archived'];

        if ($hasStageColumn) {
            $updateData['stage'] = 'archived';
        }

        Application::where('user_id', $userId)
            ->whereDate('applied_at', '<', $thresholdDate)
            ->where('status', '!=', 'archived')
            ->update($updateData);
    }
}
