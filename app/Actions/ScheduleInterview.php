<?php

namespace App\Actions;

use App\Concerns\DetectsApplicationColumns;
use App\Models\Application;

/**
 * Single-responsibility action: schedules an interview and transitions
 * the application status to 'interview'.
 */
class ScheduleInterview
{
    use DetectsApplicationColumns;

    public function execute(Application $application, array $data): void
    {
        if ($this->hasStageColumn()) {
            $application->stage = 'interview';
        }

        $application->status = 'interview';

        if ($this->hasInterviewFields()) {
            $this->fillInterviewFields($application, $data);
        }

        $application->save();
    }

    private function fillInterviewFields(Application $application, array $data): void
    {
        $isRemote = (bool) ($data['interview_is_remote'] ?? false);

        $application->interview_date      = $data['interview_date'];
        $application->interview_time      = $data['interview_time'];
        $application->interview_location  = $data['interview_location'] ?? null;
        $application->interview_is_remote = $isRemote;
        $application->interview_platform  = $isRemote  ? ($data['interview_platform'] ?? null) : null;
        $application->interview_address   = !$isRemote ? ($data['interview_address']  ?? null) : null;
    }
}
