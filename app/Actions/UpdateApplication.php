<?php

namespace App\Actions;

use App\Concerns\DetectsApplicationColumns;
use App\Models\Application;

/**
 * Single-responsibility action: updates an existing Application record.
 * Handles optional columns and interview-field hydration transparently.
 */
class UpdateApplication
{
    use DetectsApplicationColumns;

    public function execute(Application $application, array $data): void
    {
        $application->update($this->buildPayload($data));
    }

    private function buildPayload(array $data): array
    {
        $payload = [
            'company'    => $data['company'],
            'position'   => $data['position'],
            'applied_at' => $data['applied_at'],
            'job_url'    => $data['job_url'] ?? null,
            'notes'      => $data['notes']   ?? null,
        ];

        foreach ($this->optionalColumnMap() as $field => $guard) {
            if ($this->$guard()) {
                $payload[$field] = $data[$field] ?? null;
            }
        }

        if ($this->hasInterviewFields()) {
            $payload = array_merge($payload, $this->buildInterviewPayload($data));
        }

        return $payload;
    }

    /**
     * Builds the interview-related sub-payload.
     * Clears all interview fields when the application is not in interview status.
     */
    private function buildInterviewPayload(array $data): array
    {
        if (empty($data['is_interview'])) {
            return [
                'interview_date'      => null,
                'interview_time'      => null,
                'interview_location'  => null,
                'interview_is_remote' => false,
                'interview_platform'  => null,
                'interview_address'   => null,
            ];
        }

        $isRemote = (bool) ($data['interview_is_remote'] ?? false);

        return [
            'interview_date'      => $data['interview_date']     ?? null,
            'interview_time'      => $data['interview_time']     ?? null,
            'interview_location'  => $data['interview_location'] ?? null,
            'interview_is_remote' => $isRemote,
            // Platform only makes sense for remote; address only for in-person.
            'interview_platform'  => $isRemote  ? ($data['interview_platform'] ?? null) : null,
            'interview_address'   => !$isRemote ? ($data['interview_address']  ?? null) : null,
        ];
    }

    private function optionalColumnMap(): array
    {
        return [
            'city'            => 'hasCityColumn',
            'location'        => 'hasLocationColumn',
            'personal_score'  => 'hasPersonalScoreColumn',
            'salary_offered'  => 'hasSalaryOfferedColumn',
            'salary_expected' => 'hasSalaryExpectedColumn',
        ];
    }
}
