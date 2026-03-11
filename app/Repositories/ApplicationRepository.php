<?php

namespace App\Repositories;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Handles all direct database access for the Application model.
 * Business rules and orchestration belong in ApplicationService.
 */
class ApplicationRepository
{
    /**
     * Returns all applications for a user, ordered by most recently updated.
     */
    public function allForUser(int $userId): Collection
    {
        return Application::where('user_id', $userId)
            ->latest('updated_at')
            ->get();
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
