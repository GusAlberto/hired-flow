<?php

namespace App\Actions;

use App\Concerns\DetectsApplicationColumns;
use App\Models\Application;

/**
 * Single-responsibility action: moves an application to a new kanban column.
 */
class MoveApplication
{
    use DetectsApplicationColumns;

    public function execute(Application $application, string $status): void
    {
        $statusChanged = $application->status !== $status;

        if ($this->hasStageColumn()) {
            $application->stage = $status;
        }

        $application->status = $status;

        if ($statusChanged && $this->hasSortOrderColumn()) {
            $application->sort_order = $this->nextSortOrder((int) $application->user_id, $status);
        }

        $application->save();
    }

    private function nextSortOrder(int $userId, string $status): int
    {
        $max = (int) Application::query()
            ->where('user_id', $userId)
            ->where('status', $status)
            ->max('sort_order');

        return $max + 1;
    }
}
