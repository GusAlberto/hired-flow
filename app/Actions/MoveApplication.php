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
        if ($this->hasStageColumn()) {
            $application->stage = $status;
        }

        $application->status = $status;
        $application->save();
    }
}
