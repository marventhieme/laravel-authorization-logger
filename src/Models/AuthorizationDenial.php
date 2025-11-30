<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class AuthorizationDenial extends Model
{
    use Prunable;

    protected $guarded = [];

    protected $casts = [
        'logged_at' => 'datetime',
        'user_roles' => 'array',
        'request_body' => 'array',
    ];

    public function prunable()
    {
        $days = config('authorization-logger.database.prunable_after_days');

        return static::where('logged_at', '<=', now()->subDays($days));
    }
}
