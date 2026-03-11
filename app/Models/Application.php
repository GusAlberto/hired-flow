<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'company',
        'position',
        'city',
        'location',
        'stage',
        'is_favorite',
        'status',
        'applied_at',
        'job_url',
        'interview_date',
        'interview_time',
        'interview_location',
        'interview_is_remote',
        'interview_platform',
        'interview_address',
        'notes',
        'personal_score',
        'salary_offered',
        'salary_expected',
        'job_summary',
    ];

    protected $casts = [
        'applied_at' => 'date',
        'is_favorite' => 'boolean',
        'interview_date' => 'date',
        'interview_is_remote' => 'boolean',
        'personal_score' => 'integer',
        'salary_offered' => 'decimal:2',
        'salary_expected' => 'decimal:2',
    ];
}