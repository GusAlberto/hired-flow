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
        'status',
        'applied_at',
        'job_url',
        'notes',
        'personal_score',
    ];

    protected $casts = [
        'applied_at' => 'date',
        'personal_score' => 'integer',
    ];
}