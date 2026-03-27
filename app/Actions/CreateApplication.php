<?php

namespace App\Actions;

use App\Concerns\DetectsApplicationColumns;
use App\Models\Application;

/**
 * Single-responsibility action: creates a new Application record.
 * Only includes columns that actually exist in the current schema.
 */
class CreateApplication
{
    use DetectsApplicationColumns;

    public function execute(array $data, int $userId): Application
    {
        $status = 'applied';

        return Application::create([
            'user_id' => $userId,
            'status'  => $status,
            ...($this->hasSortOrderColumn() ? ['sort_order' => $this->nextSortOrder($userId, $status)] : []),
            ...$this->buildPayload($data),
        ]);
    }

    private function nextSortOrder(int $userId, string $status): int
    {
        $max = (int) Application::query()
            ->where('user_id', $userId)
            ->where('status', $status)
            ->max('sort_order');

        return $max + 1;
    }

    private function buildPayload(array $data): array
    {
        $payload = [
            'company'    => $data['company'],
            'position'   => $data['position'],
            'applied_at' => $data['applied_at'],
            'job_url'    => $data['job_url'] ?? null,
            ...($this->hasStageColumn()   ? ['stage'       => 'applied'] : []),
            ...($this->hasFavoriteColumn() ? ['is_favorite' => false]     : []),
        ];

        foreach ($this->optionalColumnMap() as $field => $guard) {
            if ($this->$guard()) {
                $payload[$field] = $data[$field] ?? null;
            }
        }

        return $payload;
    }

    /** Maps optional DB columns to their schema-guard method names. */
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
