<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Commands;

use Illuminate\Console\Command;

class LaravelAuthorizationLoggerCommand extends Command
{
    public $signature = 'laravel-authorization-logger';

    public $description = 'TODO';

    public function handle(): int
    {
        $this->comment('Does nothing yet.');

        return self::SUCCESS;
    }
}
