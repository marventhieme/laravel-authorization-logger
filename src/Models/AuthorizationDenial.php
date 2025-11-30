<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorizationDenial extends Model
{
    protected $guarded = [];

    protected $casts = [
        'logged_at' => 'datetime',
        'user_roles' => 'array',
        'request_body' => 'array',
    ];
}
