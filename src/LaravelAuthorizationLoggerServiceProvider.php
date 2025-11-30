<?php

namespace MarvenThieme\LaravelAuthorizationLogger;

use Illuminate\Support\Facades\Gate;
use MarvenThieme\LaravelAuthorizationLogger\Commands\LaravelAuthorizationLoggerCommand;
use MarvenThieme\LaravelAuthorizationLogger\Services\AuthorizationDenialLogger;
use MarvenThieme\LaravelAuthorizationLogger\Services\RequestBodySanitizer;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelAuthorizationLoggerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-authorization-logger')
            ->hasConfigFile()
            ->hasMigration('create_laravel_authorization_logger_table');
        // ->hasCommand(LaravelAuthorizationLoggerCommand::class);
    }

    public function registeringPackage()
    {
        parent::registeringPackage();

        $this->app->singleton(RequestBodySanitizer::class);
        $this->app->singleton(AuthorizationDenialLogger::class);
    }

    public function bootingPackage(): void
    {
        parent::bootingPackage();

        $this->setupAuthorizationLogging();
    }

    private function setupAuthorizationLogging(): void
    {
        Gate::after(function ($user, $ability, $result, $arguments) {
            app(AuthorizationDenialLogger::class)->log($user, $ability, $result, $arguments);
        });
    }
}
