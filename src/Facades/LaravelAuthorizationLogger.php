<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MarvenThieme\LaravelAuthorizationLogger\LaravelAuthorizationLogger
 */
class LaravelAuthorizationLogger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MarvenThieme\LaravelAuthorizationLogger\LaravelAuthorizationLogger::class;
    }
}
