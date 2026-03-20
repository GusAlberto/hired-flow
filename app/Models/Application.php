<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company',
        'position',
        'city',
        'location',
        'stage',
        'is_favorite',
        'status',
        'sort_order',
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
    ];

    protected $casts = [
        'applied_at' => 'date',
        'sort_order' => 'integer',
        'is_favorite' => 'boolean',
        'interview_date' => 'date',
        'interview_is_remote' => 'boolean',
        'personal_score' => 'integer',
        'salary_offered' => 'decimal:2',
        'salary_expected' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}