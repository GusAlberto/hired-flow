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
        'notes',
        'personal_score',
    ];

    protected $casts = [
        'applied_at' => 'date',
        'is_favorite' => 'boolean',
        'personal_score' => 'integer',
    ];
}