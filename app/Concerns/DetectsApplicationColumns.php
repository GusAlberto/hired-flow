<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Schema;

/**
 * Provides schema-guard helpers used across multiple layers (Livewire, Service, Actions).
 * Each method checks whether an optional column exists before it's referenced,
 * enabling backward-compatible behaviour when running on different migration states.
 */
trait DetectsApplicationColumns
{
    public function hasStageColumn(): bool
    {
        return Schema::hasColumn('applications', 'stage');
    }

    public function hasCityColumn(): bool
    {
        return Schema::hasColumn('applications', 'city');
    }

    public function hasLocationColumn(): bool
    {
        return Schema::hasColumn('applications', 'location');
    }

    public function hasPersonalScoreColumn(): bool
    {
        return Schema::hasColumn('applications', 'personal_score');
    }

    public function hasSalaryOfferedColumn(): bool
    {
        return Schema::hasColumn('applications', 'salary_offered');
    }

    public function hasSalaryExpectedColumn(): bool
    {
        return Schema::hasColumn('applications', 'salary_expected');
    }

    public function hasFavoriteColumn(): bool
    {
        return Schema::hasColumn('applications', 'is_favorite');
    }

    public function hasSortOrderColumn(): bool
    {
        return Schema::hasColumn('applications', 'sort_order');
    }

    public function hasInterviewFields(): bool
    {
        return Schema::hasColumns('applications', [
            'interview_date',
            'interview_time',
            'interview_location',
            'interview_is_remote',
            'interview_platform',
            'interview_address',
        ]);
    }
}
