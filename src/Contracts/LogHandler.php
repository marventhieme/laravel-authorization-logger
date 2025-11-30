<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Contracts;

use MarvenThieme\LaravelAuthorizationLogger\Objects\LogData;

interface LogHandler
{
    public function handle(LogData $logData): void;
}
