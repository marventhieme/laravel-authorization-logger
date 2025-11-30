<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Commands;

use Illuminate\Console\Command;

class LaravelAuthorizationLoggerCommand extends Command
{
    public $signature = 'laravel-authorization-logger';

    public $description = 'TODO';

    public function handle(): int
    {
        $this->comment('Does nothing yet. We could output top 10 most denied authorizations here in the future.');

        return self::SUCCESS;
    }
}
