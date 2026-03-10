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
        'status',
        'applied_at',
        'job_url',
        'notes'
    ];

    protected $casts = [
        'applied_at' => 'date',
    ];
}